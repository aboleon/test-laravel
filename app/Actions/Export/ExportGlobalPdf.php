<?php

namespace App\Actions\Export;

use App\DataTables\View\InvoiceView;
use App\DataTables\View\RefundsView;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\Ajax;

class ExportGlobalPdf {
    use Ajax;
    use ValidationTrait;

    public ?Carbon $date_start = null;
    public ?Carbon $date_end = null;
    public ?int $event_id = null;


    public function generate(): self
    {
        if (!method_exists($this, request('action'))) {
            $this->responseError("Une erreur est survenue");
            return $this;
        }

        try{
            $this->setDate()
                ->{request('action')}();
        }catch (\Exception $exception){
            $this->responseError($exception->getMessage());
        }

        return $this;
    }

    public function generateInvoiceExport(): self
    {
        if (!$this->hasErrors()) {
            $invoices = InvoiceView::where('event_id', $this->event_id)
                ->whereBetween('created_at', [$this->date_start, $this->date_end])->get();
            if(!$invoices->isEmpty()){
                $this->response['callback'] = 'exportGlobalPdf';
                return $this;
            }

            $this->responseError('Aucun PDF ne correspond à la période sélectionnée.');
        }

        return $this;
    }

    public function generateRefundExport(): self
    {
        if (!$this->hasErrors()) {
            $refunds = RefundsView::where('event_id', $this->event_id)
                ->whereBetween('created_at_raw', [$this->date_start, $this->date_end])->get();

            if(!$refunds->isEmpty()){
                $this->response['callback'] = 'exportGlobalPdf';
                return $this;
            }

            $this->responseError('Aucun PDF ne correspond à la période sélectionnée.');
        }

        return $this;
    }

    private function setDate(): self
    {
        if(!request('start')){
            $this->responseError("Veuillez selectionné une date de début");
            return $this;
        }

        $this->date_start = Carbon::createFromFormat('d/m/Y', request('start'))->startOfDay();
        $this->date_end = request('end') ? Carbon::createFromFormat('d/m/Y', request('end'))->endOfDay() : now();

        if ($this->date_start->gt($this->date_end)) {
            $this->responseError("La date de début ne peut pas être postérieure à la date de fin.");
            return $this;
        }

        $this->event_id = (int) request('event_id');

        return $this;
    }
}
