<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

trait PdfCacheableTrait
{
    // Cache the generated PDF path
    private ?string $cachedPath = null;

    /**
     * Get storage disk for PDF files
     */
    protected function getPdfStorageDisk(): string
    {
        return property_exists($this, 'pdfStorageDisk') ? $this->pdfStorageDisk : 'local';
    }

    /**
     * Get base directory for storing PDFs
     */
    protected function getPdfStorageDirectory(): string
    {
        return property_exists($this, 'pdfStorageDirectory') ? $this->pdfStorageDirectory : 'pdfs';
    }

    /**
     * Ensure PDF is generated and cached
     */
    protected function ensurePdfIsCached(): void
    {
        // Always create the PDF instance for compatibility with DomPdf trait
        $this->pdf = Pdf::loadView($this->getPdfView(), $this->data);

        if (!$this->canBeCached()) {
            return;
        }

        $checksumData = $this->getChecksumData();
        $this->cachedPath = $this->getCachedPdf($this->identifier, $checksumData, function() {
            return $this->pdf->output();
        });
    }

    /**
     * Get cached content or generate from PDF
     */
    protected function getCachedContent(): string
    {
        if ($this->cachedPath && Storage::disk($this->getPdfStorageDisk())->exists($this->cachedPath)) {
            return Storage::disk($this->getPdfStorageDisk())->get($this->cachedPath);
        }

        return $this->pdf->output();
    }

    /**
     * Get cached PDF path
     */
    public function getCachedPdfPath(): ?string
    {
        return $this->cachedPath;
    }

    /**
     * Force regenerate the cached PDF
     */
    public function regenerateCachedPdf(): ?string
    {
        $this->deleteCachedPdf($this->identifier);
        $this->ensurePdfIsCached();
        return $this->cachedPath;
    }

    /**
     * Get or generate cached PDF
     */
    public function getCachedPdf(string $identifier, array $checksumData, ?callable $generator = null): ?string
    {
        $checksum = $this->generateChecksum($checksumData);
        $pdfPath = $this->getPdfPath($identifier, $checksum);

        if (Storage::disk($this->getPdfStorageDisk())->exists($pdfPath)) {
            return $pdfPath;
        }

        $this->cleanupOldVersions($identifier);

        try {
            $pdfContent = $generator ? $generator() : $this->generatePdfContent();

            $directory = dirname($pdfPath);
            Storage::disk($this->getPdfStorageDisk())->makeDirectory($directory);
            Storage::disk($this->getPdfStorageDisk())->put($pdfPath, $pdfContent);

            return $pdfPath;
        } catch (\Exception $e) {
            \Log::error('Failed to generate cached PDF for ' . $identifier . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate checksum from data
     */
    protected function generateChecksum(array $data): string
    {
        return substr(md5(json_encode($data)), 0, 8);
    }

    /**
     * Get PDF file path
     */
    protected function getPdfPath(string $identifier, string $checksum): string
    {
        $subdirectory = $this->getPdfSubdirectory();
        $baseDir = $this->getPdfStorageDirectory();
        return trim($baseDir . '/' . $subdirectory . '/' . $identifier . '_' . $checksum . '.pdf', '/');
    }

    /**
     * Get subdirectory for organizing PDFs (override in implementation)
     */
    protected function getPdfSubdirectory(): string
    {
        return '';
    }

    /**
     * Clean up old versions of a PDF
     */
    protected function cleanupOldVersions(string $identifier): void
    {
        $directory = $this->getPdfStorageDirectory() . '/' . $this->getPdfSubdirectory();
        $files = Storage::disk($this->getPdfStorageDisk())->files($directory);

        foreach ($files as $file) {
            if (strpos(basename($file), $identifier . '_') === 0) {
                Storage::disk($this->getPdfStorageDisk())->delete($file);
            }
        }
    }

    /**
     * Delete cached PDF
     */
    public function deleteCachedPdf(string $identifier): void
    {
        $this->cleanupOldVersions($identifier);
    }

    /**
     * Generate PDF content
     */
    protected function generatePdfContent(): string
    {
        if (method_exists($this, 'output')) {
            return $this->output();
        }

        throw new \BadMethodCallException(
            'You must either implement generatePdfContent() method or pass a generator function to getCachedPdf()'
        );
    }

    /**
     * Methods that must be implemented by the class using this trait
     */
    abstract protected function getPdfView(): string;
    abstract protected function canBeCached(): bool;
    abstract protected function getChecksumData(): array;
}
