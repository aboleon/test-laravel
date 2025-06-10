<?php

namespace App\Printers\PDF;

use App\Models\Order;
use App\Models\Order\Refund;
use App\Traits\EventCommons;
use App\Traits\PdfCacheableTrait;
use Illuminate\Http\Response;
use MetaFramework\Traits\DomPdf;

class Refundable
{
    use DomPdf;
    use EventCommons;
    use PdfCacheableTrait;

    private array $data = [];
    private Order $order;
    private ?Refund $refund = null;
    private ?\App\Accessors\OrderAccessor $orderAccessor = null;

    // Override PDF storage directory for refunds
    protected string $pdfStorageDirectory = 'refunds';

    public function __construct(public string $identifier)
    {
        $this->setData();
        $this->ensurePdfIsCached();
    }

    /**
     * Get PDF view name
     */
    protected function getPdfView(): string
    {
        return 'pdf.refundable';
    }

    /**
     * Check if PDF can be cached
     */
    protected function canBeCached(): bool
    {
        return $this->refund !== null;
    }

    /**
     * Override output method to use cached file when available
     */
    public function output(): string
    {
        // If we have a cached file, use it
        if ($this->cachedPath && \Storage::disk($this->getPdfStorageDisk())->exists($this->cachedPath)) {
            return \Storage::disk($this->getPdfStorageDisk())->get($this->cachedPath);
        }

        // Otherwise use the PDF instance
        return $this->pdf->output();
    }

    /**
     * Override stream method to use cached file
     */
    public function stream(): Response
    {
        $content = $this->output();

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="refund.pdf"',
        ]);
    }

    /**
     * Override download method to use cached file
     */
    public function download(string $filename = null): Response
    {
        $filename = $filename ?? $this->getDefaultFilename();
        $content = $this->output();

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get default filename for download
     */
    protected function getDefaultFilename(): string
    {
        return "Avoir_DivineID_N-" . ($this->refund?->refund_number ?? 'unknown') . ".pdf";
    }

    /**
     * Get data for checksum calculation
     */
    protected function getChecksumData(): array
    {
        $data = [
            'refund_id' => $this->refund->id,
            'refund_uuid' => $this->refund->uuid,
            'refund_number' => $this->refund->refund_number,
            'refund_amount' => $this->refund->amount,
            'refund_status' => $this->refund->status,
            'refund_updated_at' => $this->refund->updated_at?->toDateTimeString(),
            'order_id' => $this->order->id,
            'order_uuid' => $this->order->uuid,
        ];

        // Add refund items checksum
        if ($this->refund->items) {
            $data['items_checksum'] = md5($this->refund->items->toJson());
        }

        return $data;
    }

    /**
     * Get detailed debug information about the cached PDF
     */
    public function getDebugInfo(): array
    {
        $checksumData = $this->getChecksumData();
        $checksum = $this->generateChecksum($checksumData);
        $expectedPath = $this->getPdfPath($this->identifier, $checksum);

        return [
            'refund_details' => [
                'identifier' => $this->identifier,
                'refund_id' => $this->refund?->id,
                'refund_number' => $this->refund?->refund_number,
                'order_id' => $this->order?->id,
            ],
            'cache_details' => [
                'checksum' => $checksum,
                'cached_path' => $this->cachedPath,
                'expected_path' => $expectedPath,
                'paths_match' => $this->cachedPath === $expectedPath,
                'full_path' => $this->cachedPath ? storage_path('app/' . $this->cachedPath) : null,
                'subdirectory' => $this->getPdfSubdirectory(),
                'storage_disk' => $this->getPdfStorageDisk(),
                'storage_directory' => $this->getPdfStorageDirectory(),
            ],
            'file_status' => [
                'exists' => $this->cachedPath ? \Storage::disk($this->getPdfStorageDisk())->exists($this->cachedPath) : false,
                'size_bytes' => $this->cachedPath && \Storage::disk($this->getPdfStorageDisk())->exists($this->cachedPath)
                    ? \Storage::disk($this->getPdfStorageDisk())->size($this->cachedPath) : null,
                'last_modified' => $this->cachedPath && \Storage::disk($this->getPdfStorageDisk())->exists($this->cachedPath)
                    ? date('Y-m-d H:i:s', \Storage::disk($this->getPdfStorageDisk())->lastModified($this->cachedPath)) : null,
            ],
            'checksum_components' => $checksumData,
        ];
    }

    public function setData(): void
    {
        $this->refund = Refund::firstWhere('uuid', $this->identifier);

        if ($this->refund) {
            $this->order = $this->refund->order;
            $this->orderAccessor = (new \App\Accessors\OrderAccessor($this->order));
        } else {
            abort(404, "Avoir not trouvé pour la requête " . $this->identifier);
        }

        $this->data = [
            'refund' => $this->refund->load('items'),
            'order' => $this->order,
            'address' => implode('<br>', $this->orderAccessor->invoiceableAddress()),
            'orderAccessor' => $this->orderAccessor,
            'banner' => $this->getBanner($this->order->event, 'thumbnail'),
        ];
    }

    public function __invoke(): Response
    {
        if (request()->has('download')) {
            return $this->download();
        }
        return $this->stream();
    }
}
