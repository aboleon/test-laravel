<?php

namespace App\Traits;

use Exception;
use iio\libmergepdf\Merger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Log;
use MetaFramework\Traits\Ajax;
use MetaFramework\Traits\Responses;
use ZipArchive;

trait GlobalExportTrait
{
    use Ajax;
    use Responses;

    /**
     * Export date range and event properties
     */
    public ?Carbon $date_start = null;
    public ?Carbon $date_end = null;
    public ?int $event_id = null;

    /**
     * Set variables from request
     */
    protected function setExportVars(): self
    {
        try {
            $this->date_start = Carbon::createFromFormat('d/m/Y', request('start'))->startOfDay();
            $this->date_end = request('end') ? Carbon::createFromFormat('d/m/Y', request('end'))->endofDay() : now();
        } catch (\Throwable $e) {
            $this->responseException($e, "Les deux dates sont nécessaires");
            return $this;
        }

        // Event ID is optional
        $this->event_id = request()->filled('event_id') ? (int)request('event_id') : null;

        return $this;
    }

    /**
     * Process export based on format selection
     */
    protected function processExport(array $config)
    {
        $exportFormat = request('export_format', 'zip');

        switch ($exportFormat) {
            case 'pdf':
                return $this->processMergedPdfExport($config);

            case 'csv':
                return $this->processCsvExport($config);

            case 'zip':
            default:
                return $this->processZipExport($config);
        }
    }

    /**
     * Process CSV export (placeholder for future implementation)
     */
    protected function processCsvExport(array $config)
    {
        $this->responseError("L'export CSV n'est pas encore implémenté.");
        return $this;
    }

    /**
     * Process ZIP export
     */
    protected function processZipExport(array $config)
    {
        $this->enableAjaxMode();
        $this->responseElement('auto_download', true);
        $this->responseElement('callback', 'processExport');

        try {
            // Create unique export identifier
            $exportId = $this->generateExportId($config['prefix']);
            $zipPath = 'exports/' . $exportId . '.zip';

            // Check if export already exists
            if (Storage::disk('local')->exists($zipPath)) {
                return $this->handleExistingExport($zipPath, $config['filename_prefix'], 'zip');
            }

            // Create ZIP file
            Storage::disk('local')->makeDirectory('exports');
            $zip = new ZipArchive();
            $tempZipPath = storage_path('app/' . $zipPath);

            if ($zip->open($tempZipPath, ZipArchive::CREATE) !== true) {
                $this->responseError("Impossible de créer le fichier zip de l'export");
                return $this;
            }

            // Process items
            $result = $this->processItemsForExport($config, function($item, $pdfPath) use ($zip, $config) {
                $filename = $this->generateFilename($item, $config);
                $zip->addFile(storage_path('app/' . $pdfPath), $filename);
            });

            if ($result['count'] === 0) {
                $this->responseWarning("Aucun fichier n'est trouvé pour cette période.");
                return $this;
            }

            // Add summary
            $summaryContent = $this->generateExportSummary($result['count'], $result['errors'], $config['type']);
            $zip->addFromString('export_summary.txt', $summaryContent);
            $zip->close();

            // Log completion
            Log::info("Export {$config['type']} terminé.", [
                'event_id' => $this->event_id,
                'date_range' => [$this->date_start, $this->date_end],
                'total_processed' => $result['count'],
                'errors' => count($result['errors']),
            ]);

            // Return response
            $downloadFilename = $this->generateExportFilename($config['filename_prefix'], 'zip');
            $downloadUrl = route('panel.download.file', [
                'path' => $zipPath,
                'filename' => $downloadFilename,
            ]);

            $this->responseElement('download_url', $downloadUrl);
            $this->responseElement('processed_count', $result['count']);
            $this->responseElement('error_count', count($result['errors']));

            $message = "L'export est terminé. Il contient {$result['count']} fichiers.";
            if (count($result['errors']) > 0) {
                $message .= " " . count($result['errors']) . " fichiers en erreur.";
            }
            $this->responseSuccess($message);

        } catch (Exception $e) {
            $this->responseException($e, "Une erreur est survenue lors de l'export");
        }

        return $this;
    }

    /**
     * Process merged PDF export
     */
    protected function processMergedPdfExport(array $config)
    {
        $this->enableAjaxMode();
        $this->responseElement('auto_download', true);
        $this->responseElement('callback', 'processExport');

        try {
            // Count total items
            $totalCount = call_user_func($config['count_callback']);

            if ($totalCount === 0) {
                $this->responseWarning("Aucun fichier n'est trouvé pour cette période.");
                return $this;
            }

            // Check size limit
            if ($totalCount > 100) {
                $this->responseWarning("Trop de {$config['type']} ({$totalCount}) pour une fusion directe. Veuillez utiliser l'export ZIP.");
                return $this;
            }

            // Create export identifier
            $exportId = $this->generateExportId($config['prefix'] . '-merged');
            $pdfPath = 'exports/' . $exportId . '.pdf';

            // Check if already exists
            if (Storage::disk('local')->exists($pdfPath)) {
                return $this->handleExistingExport($pdfPath, $config['filename_prefix'], 'pdf');
            }

            // Process with memory optimization
            $result = $this->processMergedPdfWithOptimization($config, $pdfPath);

            if ($result['count'] === 0) {
                $this->responseError("Aucun fichier n'a pu être traité.");
                return $this;
            }

            // Return response
            $downloadFilename = $this->generateExportFilename($config['filename_prefix'], 'pdf');
            $downloadUrl = route('panel.download.file', [
                'path' => $pdfPath,
                'filename' => $downloadFilename,
            ]);

            $this->responseElement('download_url', $downloadUrl);
            $this->responseElement('processed_count', $result['count']);
            $this->responseElement('error_count', count($result['errors']));

            $message = "Export PDF terminé. {$result['count']} {$config['type']} fusionnés.";
            if (count($result['errors']) > 0) {
                $message .= " " . count($result['errors']) . " erreurs rencontrées.";
            }
            $this->responseSuccess($message);

        } catch (Exception $e) {
            $this->responseException($e, "Erreur lors de la fusion des PDF. Veuillez utiliser l'export ZIP.");
        }

        return $this;
    }

    /**
     * Generate unique export identifier
     */
    protected function generateExportId(string $prefix): string
    {
        $parts = [$prefix];

        if ($this->event_id) {
            $parts[] = $this->event_id;
        }

        $parts[] = md5($this->date_start . $this->date_end);

        return implode('-', $parts);
    }

    /**
     * Generate export filename
     */
    protected function generateExportFilename(string $prefix, string $format): string
    {
        $parts = [$prefix];

        if ($this->event_id) {
            $parts[] = 'event_' . $this->event_id;
        }

        $parts[] = $this->date_start->format('Y-m-d');
        $parts[] = 'to';
        $parts[] = $this->date_end->format('Y-m-d');

        return implode('_', $parts) . '.' . $format;
    }

    /**
     * Process items for export
     */
    protected function processItemsForExport(array $config, callable $processor)
    {
        $processedCount = 0;
        $errors = [];

        $query = call_user_func($config['query_callback']);

        $query->chunk(100, function($items) use (&$processedCount, &$errors, $config, $processor) {
            foreach ($items as $item) {
                try {
                    $pdfPrinter = new $config['printer_class']($item->uuid);
                    $pdfPath = $pdfPrinter->getCachedPdfPath();

                    if ($pdfPath && Storage::disk('local')->exists($pdfPath)) {
                        $processor($item, $pdfPath);
                        $processedCount++;

                        if ($processedCount % 100 === 0) {
                            Log::info("{$config['type']} export progress: {$processedCount} processed");
                        }
                    } else {
                        $errors[] = "Failed to generate PDF for {$config['type']} {$item->id} (UUID: {$item->uuid})";
                    }
                } catch (Exception $e) {
                    $errors[] = "Erreur sur {$config['type']} {$item->id}: " . $e->getMessage();
                    Log::error("{$config['type']} export error", [
                        'id' => $item->id,
                        'uuid' => $item->uuid,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        return ['count' => $processedCount, 'errors' => $errors];
    }

    /**
     * Process merged PDF with memory optimization
     */
    protected function processMergedPdfWithOptimization(array $config, string $outputPath)
    {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 1200);

        $tempDir = storage_path('app/temp/merge_' . time());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $fileCount = 0;

        // Copy PDFs to temp directory
        $result = $this->processItemsForExport($config, function($item, $pdfPath) use ($tempDir, &$fileCount) {
            $tempFile = $tempDir . '/' . sprintf('%05d', $fileCount++) . '.pdf';
            copy(storage_path('app/' . $pdfPath), $tempFile);
        });

        if ($result['count'] === 0) {
            $this->cleanupTempDirectory($tempDir);
            return $result;
        }

        // Try Ghostscript first
        $gsSuccess = $this->tryGhostscriptMerge($tempDir, storage_path('app/' . $outputPath));

        if (!$gsSuccess) {
            // Fallback to PHP merger
            $this->mergePdfsInSmallBatches($tempDir, storage_path('app/' . $outputPath), 5);
        }

        $this->cleanupTempDirectory($tempDir);

        return $result;
    }

    /**
     * Handle existing export
     */
    protected function handleExistingExport(string $path, string $prefix, string $format)
    {
        $this->responseSuccess("Export déjà disponible, téléchargement en cours...");

        $downloadFilename = $this->generateExportFilename($prefix, $format);
        $downloadUrl = route('panel.download.file', [
            'path' => $path,
            'filename' => $downloadFilename,
        ]);

        $this->responseElement('download_url', $downloadUrl);
        return $this;
    }

    /**
     * Generate filename for individual item
     */
    protected function generateFilename($item, array $config): string
    {
        $parts = [];

        // Add prefix and number
        $numberField = $config['number_field'] ?? 'id';
        $prefix = $config['item_prefix'] ?? 'DOC';

        if (!empty($item->$numberField)) {
            $parts[] = $prefix . '-' . $item->$numberField;
        } else {
            $parts[] = $prefix . '-' . $item->id;
        }

        // Add date
        $dateField = $config['date_field'] ?? 'created_at';
        $parts[] = date('Y-m-d', strtotime($item->$dateField));

        // Add name if available
        $nameField = $config['name_field'] ?? 'customer_name';
        if (!empty($item->$nameField)) {
            $name = preg_replace('/[^A-Za-z0-9\-]/', '_', $item->$nameField);
            $name = substr($name, 0, 30);
            $parts[] = $name;
        }

        return implode('_', $parts) . '.pdf';
    }

    /**
     * Generate export summary with type
     */
    protected function generateExportSummary(int $processedCount, array $errors, string $type): string
    {
        $summaryContent = ucfirst($type) . " Export Summary\n";
        $summaryContent .= str_repeat("=", strlen($summaryContent) - 1) . "\n\n";
        $summaryContent .= "Export Date: " . now()->format('Y-m-d H:i:s') . "\n";

        if ($this->event_id) {
            $summaryContent .= "Event ID: {$this->event_id}\n";
        }

        $summaryContent .= "Date Range: {$this->date_start} to {$this->date_end}\n\n";
        $summaryContent .= "Results:\n";
        $summaryContent .= "--------\n";
        $summaryContent .= "Total {$type} processed: {$processedCount}\n";
        $summaryContent .= "Errors encountered: " . count($errors) . "\n\n";

        if (!empty($errors)) {
            $summaryContent .= "Error Details:\n";
            $summaryContent .= "--------------\n";
            foreach ($errors as $error) {
                $summaryContent .= "- {$error}\n";
            }
        }

        return $summaryContent;
    }

    // Ghostscript methods
    protected function tryGhostscriptMerge($tempDir, $outputPath)
    {
        $gsPath = $this->findGhostscript();
        if (!$gsPath) {
            return false;
        }

        try {
            $files = glob($tempDir . '/*.pdf');
            if (empty($files)) {
                return false;
            }

            $command = sprintf(
                '%s -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -sOutputFile=%s %s 2>&1',
                escapeshellarg($gsPath),
                escapeshellarg($outputPath),
                implode(' ', array_map('escapeshellarg', $files))
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputPath)) {
                Log::info("PDFs merged successfully using Ghostscript");
                return true;
            }

            Log::warning("Ghostscript merge failed: " . implode("\n", $output));
            return false;

        } catch (Exception $e) {
            Log::error("Ghostscript merge error: " . $e->getMessage());
            return false;
        }
    }

    protected function findGhostscript()
    {
        $possiblePaths = [
            '/usr/bin/gs',
            '/usr/local/bin/gs',
            'C:\\Program Files\\gs\\gs9.56.1\\bin\\gswin64c.exe',
            'C:\\Program Files (x86)\\gs\\gs9.56.1\\bin\\gswin32c.exe',
            'gs',
        ];

        foreach ($possiblePaths as $path) {
            if (is_executable($path)) {
                return $path;
            }
            if (PHP_OS_FAMILY !== 'Windows') {
                exec("which {$path} 2>/dev/null", $output);
                if (!empty($output[0]) && is_executable($output[0])) {
                    return $output[0];
                }
            }
        }

        return null;
    }

    protected function mergePdfsInSmallBatches($tempDir, $outputPath, $batchSize = 5)
    {
        $files = glob($tempDir . '/*.pdf');
        if (empty($files)) {
            throw new Exception("No PDF files found in temp directory");
        }

        if (count($files) <= $batchSize) {
            $merger = new Merger();
            foreach ($files as $file) {
                $merger->addFile($file);
            }
            file_put_contents($outputPath, $merger->merge());
            return;
        }

        $batchNumber = 1;
        $batchFiles = [];

        foreach (array_chunk($files, $batchSize) as $batch) {
            $merger = new Merger();
            foreach ($batch as $file) {
                $merger->addFile($file);
            }

            $batchFile = $tempDir . '/batch_' . sprintf('%03d', $batchNumber) . '.pdf';
            file_put_contents($batchFile, $merger->merge());
            $batchFiles[] = $batchFile;
            $batchNumber++;

            unset($merger);
            gc_collect_cycles();
        }

        if (count($batchFiles) > 1) {
            $this->mergePdfsInSmallBatches($tempDir, $outputPath, $batchSize);
        } else {
            copy($batchFiles[0], $outputPath);
        }
    }

    protected function cleanupTempDirectory($tempDir)
    {
        try {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($tempDir);
        } catch (Exception $e) {
            Log::warning("Failed to cleanup temp directory: " . $e->getMessage());
        }
    }
}
