<?php

namespace App\Http\Controllers;

use App\DataTables\View\InvoiceView;
use App\DataTables\View\RefundsView;
use App\MailTemplates\PdfPrinter;
use App\Printers\PDF\Invoice;
use App\Printers\PDF\Refundable;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\View\View;
use MetaFramework\Traits\Responses;

class PDFGlobalExportController extends Controller
{
    use Responses;

    public ?Carbon $date_start = null;
    public ?Carbon $date_end = null;
    public ?int $event_id = null;

    public function globalExport()
    {
        if (!method_exists($this, request('action'))) {
            $this->responseError("Une erreur est survenue");
            return $this->fetchResponse();
        }

        try{
            $this->date_start = Carbon::createFromFormat('d/m/Y', request('start'))->startOfDay();
            $this->date_end = request('end') ? Carbon::createFromFormat('d/m/Y', request('end'))->endofDay() : now();

            $this->event_id = (int) request('event_id');
            return $this->{request('action')}();
        }catch (\Exception $exception){
            $this->responseError($exception->getMessage());
        }

        return $this->fetchResponse();
    }

    public function generateInvoiceExport(): Response
    {
        $invoices = InvoiceView::where('event_id', $this->event_id)
            ->whereBetween('created_at', [$this->date_start, $this->date_end])
            ->get();
        $merger = new Merger();

        foreach ($invoices as $invoice) {
            $pdf = (new Invoice($invoice->uuid))->output();
            $merger->addRaw($pdf);
        }

        $mergedPdf = $merger->merge();

        return response($mergedPdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoices.pdf"');
    }

    public function generateRefundExport(): Response
    {
        $refunds = RefundsView::where('event_id', $this->event_id)
            ->whereBetween('created_at_raw', [$this->date_start, $this->date_end])->get();
        $merger = new Merger();

        foreach ($refunds as $refund) {
            $pdf = (new Refundable($refund->uuid))->output();
            $merger->addRaw($pdf);
        }

        $mergedPdf = $merger->merge();

        return response($mergedPdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoices.pdf"');
    }
}
