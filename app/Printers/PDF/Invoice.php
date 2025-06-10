<?php

namespace App\Printers\PDF;

use App\Accessors\Accounts;
use App\Accessors\OrderAccessor;
use App\Models\Order;
use App\Traits\EventCommons;
use App\Traits\PdfCacheableTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use MetaFramework\Traits\DomPdf;

class Invoice
{
    use DomPdf;
    use EventCommons;
    use PdfCacheableTrait;

    private bool $isReceipt = false;
    private array $data = [];
    protected ?Order $order = null;
    private ?Collection $services = null;
    private ?OrderAccessor $orderAccessor = null;
    private array $hotels = [];
    private int $paid = 0;

    protected ?\App\Models\Invoice $invoice = null;
    private bool $proforma = false;

    // Override PDF storage directory for invoices
    protected string $pdfStorageDirectory = 'invoices';

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
        return 'pdf.invoice';
    }

    /**
     * Check if PDF can be cached
     */
    protected function canBeCached(): bool
    {
        return $this->order !== null;
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
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }

    /**
     * Override download method to use cached file
     */
    public function download(string $filename = 'invoice.pdf'): Response
    {
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
        if ($this->isReceipt) {
            return "Recu_DivineID_N-" . ($this->invoice?->invoice_number ?? $this->order->id) . ".pdf";
        }

        if ($this->proforma) {
            return "Facture_proforma_DivineID_N-" . ($this->invoice?->invoice_number ?? $this->order->id) . ".pdf";
        }

        return "Facture_DivineID_N-" . ($this->invoice?->invoice_number ?? $this->order->id) . ".pdf";
    }

    /**
     * Get data for checksum calculation
     */
    protected function getChecksumData(): array
    {
        $data = [
            'order_id' => $this->order->id,
            'order_uuid' => $this->order->uuid,
            'order_total' => $this->order->total,
            'order_total_vat' => $this->order->total_vat,
            'order_status' => $this->order->status,
            'order_updated_at' => $this->order->updated_at?->toDateTimeString(),
            'paid_amount' => $this->paid,
            'is_receipt' => $this->isReceipt,
            'is_proforma' => $this->proforma,
        ];

        if ($this->invoice) {
            $data['invoice_id'] = $this->invoice->id;
            $data['invoice_number'] = $this->invoice->invoice_number;
            $data['invoice_updated_at'] = $this->invoice->updated_at?->toDateTimeString();
        }

        if ($this->order->carts) {
            $data['carts_checksum'] = md5($this->order->carts->toJson());
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
            'invoice_details' => [
                'identifier' => $this->identifier,
                'order_id' => $this->order?->id,
                'invoice_id' => $this->invoice?->id,
                'invoice_number' => $this->invoice?->invoice_number,
                'is_receipt' => $this->isReceipt,
                'is_proforma' => $this->proforma,
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

    public function setAsReceipt(): self
    {
        $this->isReceipt = true;
        return $this;
    }

    public function setData(): void
    {
        $this->proforma = request()->filled('proforma');

        $this->order = Order::where('uuid', $this->identifier)->first();

        if ($this->order) {
            $this->services      = $this->order->event->sellableService->load('event.services');
            $this->hotels        = $this->order->event->accommodation->load('hotel')->mapWithKeys(fn($item) => [$item->id => $item->hotel->name.' '.($item->hotel->stars ? $item->hotel->stars.'*' : '').$item->title])->toArray();
            $this->orderAccessor = (new OrderAccessor($this->order));
            $this->paid          = $this->order->payments->sum('amount');

            if ($this->proforma) {
                $this->checkProforma();
            } else {
                $this->invoice = $this->order->invoice();
            }
        } else {
            abort(404, "Order not found with uuid ".$this->identifier);
        }

        $documentTtitle = $this->isReceipt ? __('front/order.receipt')
            : (($this->proforma or request()->has('proforma')) ? 'Proforma '.$this->order->id.($this->invoice?->id ? '-'.$this->invoice?->id : '') : __('front/order.invoice'));

        $account = $this->orderAccessor->account();

        $this->data = [
            'invoice'               => $this->invoice,
            'proforma'              => $this->proforma,
            'order'                 => $this->order,
            'address'               => implode('<br>', $this->orderAccessor->invoiceableAddress()),
            'services'              => $this->services,
            'orderAccessor'         => $this->orderAccessor,
            'hotels'                => $this->hotels,
            'totalsFromCarts'       => $this->orderAccessor->computeOrderTotalsFromCarts(),
            'totalsFromOrder'       => $this->orderAccessor->getOrderTotals(),
            'amendedOrder'          => $this->order->amendedOrder,
            'paid'                  => $this->paid,
            'vatSubtotalsFromCarts' => $this->orderAccessor->vatSubtotalsByVat(),
            'banner'                => $this->getBanner($this->order->event, 'thumbnail'),
            'documentTitle'         => $documentTtitle,
            'isReceipt'             => $this->isReceipt,
            'lg'                    => $account['lg'],
        ];

        $this->data['invoiceTotals'] = ($this->data['amendedOrder'] or $this->orderAccessor->isFrontGroupOrder()) ? $this->data['totalsFromOrder'] : $this->data['totalsFromCarts'];

        $this->data['invoiceVatTotals'] = $this->data['amendedOrder']
            ? [$this->order->accommodation->first()?->vat_id => $this->order->total_vat]
            : $this->data['vatSubtotalsFromCarts'];
    }

    private function checkProforma(): void
    {
        if (request()->filled('proforma')) {
            $this->invoice = \App\Models\Invoice::query()->where(['order_id' => $this->order->id, 'id' => (int)request('proforma')])->first();
        }
    }

    public function __invoke(): Response
    {
        if (request()->has('download')) {
            return $this->download("Facture ".($this->proforma ? 'proforma ' : '')."DivineID N-".$this->invoice->invoice_number.".pdf");
        }

        return $this->stream();
    }
}
