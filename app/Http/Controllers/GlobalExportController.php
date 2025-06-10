<?php

namespace App\Http\Controllers;

use App\DataTables\View\InvoiceView;
use App\DataTables\View\RefundsView;
use App\Printers\PDF\Invoice;
use App\Printers\PDF\Refundable;
use App\Traits\GlobalExportTrait;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Log;

class GlobalExportController extends Controller
{
    use GlobalExportTrait;

    public function globalExport()
    {
        if (!method_exists($this, request('action'))) {
            $this->responseError("Une erreur est survenue");
            return $this;
        }

        try {
            $this->setExportVars();
            return $this->{request('action')}();
        } catch (Exception $exception) {
            $this->responseError($exception->getMessage());
        }

        return $this;
    }

    /**
     * Generate and export invoices
     */
    public function generateInvoiceExport()
    {
        $this->setExportVars();
        if ($this->hasErrors()) {
            return $this;
        }

        $config = [
            'type' => 'invoices',
            'prefix' => 'invoice-export',
            'filename_prefix' => 'factures',
            'printer_class' => Invoice::class,
            'item_prefix' => 'INV',
            'number_field' => 'invoice_number',
            'date_field' => 'created_at',
            'name_field' => 'customer_name',
            'query_callback' => function() {
                $query = InvoiceView::query()
                    ->whereBetween('created_at', [$this->date_start, $this->date_end]);

                // Add event filter only if event_id is provided
                if ($this->event_id) {
                    $query->where('event_id', $this->event_id);
                }

                return $query;
            },
            'count_callback' => function() {
                $query = InvoiceView::query()
                    ->whereBetween('created_at', [$this->date_start, $this->date_end]);

                if ($this->event_id) {
                    $query->where('event_id', $this->event_id);
                }

                return $query->count();
            }
        ];

        return $this->processExport($config);
    }

    /**
     * Generate and export refunds
     */
    public function generateRefundExport()
    {
        $this->setExportVars();
        if ($this->hasErrors()) {
            return $this;
        }

        $config = [
            'type' => 'refunds',
            'prefix' => 'refund-export',
            'filename_prefix' => 'remboursements',
            'printer_class' => Refundable::class,
            'item_prefix' => 'RFD',
            'number_field' => 'refund_number',
            'date_field' => 'created_at_raw',
            'name_field' => 'customer_name',
            'query_callback' => function() {
                $query = RefundsView::query()
                    ->whereBetween('created_at_raw', [$this->date_start, $this->date_end]);

                // Add event filter only if event_id is provided
                if ($this->event_id) {
                    $query->where('event_id', $this->event_id);
                }

                return $query;
            },
            'count_callback' => function() {
                $query = RefundsView::query()
                    ->whereBetween('created_at_raw', [$this->date_start, $this->date_end]);

                if ($this->event_id) {
                    $query->where('event_id', $this->event_id);
                }

                return $query->count();
            }
        ];

        return $this->processExport($config);
    }

    /**
     * Stream merged PDF (for direct streaming if needed)
     */
    public function streamMergedPdf($token)
    {
        $params = cache()->get('pdf_stream_' . $token);
        if (!$params) {
            abort(404, 'Invalid or expired token');
        }

        $this->event_id = $params['event_id'] ?? null;
        $this->date_start = Carbon::parse($params['date_start']);
        $this->date_end = Carbon::parse($params['date_end']);
        $type = $params['type'] ?? 'invoices';

        $filename = request('filename', 'merged.pdf');

        return response()->stream(function() use ($type) {
            // Implementation depends on type
            // You can add streaming logic here if needed
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Clean up old exports (can be called from a scheduled command)
     */
    public function cleanupOldExports(int $daysToKeep = 30): int
    {
        $cutoff = now()->subDays($daysToKeep);
        $deletedCount = 0;

        $files = Storage::disk('local')->files('exports');
        foreach ($files as $file) {
            if (Storage::disk('local')->lastModified($file) < $cutoff->timestamp) {
                Storage::disk('local')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
