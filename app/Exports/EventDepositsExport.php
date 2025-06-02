<?php

namespace App\Exports;

use App\DataTables\View\EventDepositView;
use App\Enum\EventDepositStatus;
use App\Enum\OrderType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Collection;


class EventDepositsExport implements FromView
{
    private QueryBuilder $query;
    private Collection $data;

    public function __construct(public Event $event)
    {
        $this->generateQuery();
        $this->getData();
    }

    public function view(): View
    {
        return view('event-deposits.export.event-deposit', ['data' => $this->data]);
    }

    public function generateQuery(): void
    {
        $model = new EventDepositView();
        $this->query  = $model->newQuery()
            ->where('event_id', $this->event->id);

        $status = false;
        if(request()->has('status')){
            $legit_status = EventDepositStatus::keys();
            $status = in_array(request('status'), $legit_status, true) ? request('status') : null;
        }

        if ($status) {
            $this->query->where('status', $status);
        }

        //add Grand caution seulement !
        $this->query->where('shoppable_type', OrderType::GRANTDEPOSIT->value);

        //add search filter dataTable !! (pas tout les champ utilisé dans la data)
        if(request()->has('search')){
            $this->query->where(function ($query) {
                $search = strtolower(request('search'));
                $query->whereRaw('LOWER(`id`) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(`date_fr`) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(`shoppable_label`) LIKE ?', ["%{$search}%"]);
            });
        }

        $this->query->orderBy('order_id', 'desc');
    }


    public function getData(): void
    {
        $this->data = $this->query->get();
    }
}
