<?php

namespace App\Exports\Accounting;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Collection;


class AccountsInvoicesExport implements FromView
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
        return view('panel.accounting.export.accounting-invoices', ['data' => $this->data]);
    }

    public function generateQuery(): void
    {
        // the query
    }


    public function getData(): void
    {
        $this->data = $this->query->get();
    }
}
