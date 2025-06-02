<?php

namespace App\DataTables;

use App\DataTables\View\EventSellableServiceView;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventSellableDataTable extends DataTable
{
    use Common;

    public function __construct(
        private readonly Event $event,
        private readonly bool $show_only_pec = false,
    ) {
        parent::__construct();
    }

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="'.$row->id.'"></div>';
            })
            ->editColumn('total_bookings_count', function ($data) {
                return view('events.manager.sellable.datatable.total-bookings-count')->with([
                    'data' => $data
                ]);
            })
            ->addColumn('pec_eligible', function ($data) {
                return '<i class="bi bi-check-circle-fill '.($data->pec_eligible ? 'text-success' : 'text-secondary opacity-50').'">';
            })
            ->addColumn('published', function ($data) {
                return '<i class="bi bi-check-circle-fill '.($data->published ? 'text-success' : 'text-secondary opacity-50').'">';
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.sellable.datatable.action')->with([
                    'data' => $data,
                ])->render();
            })
            ->orderColumn('published', 'published $1')
            ->orderColumn('pec_eligible', 'pec_eligible $1')
            ->rawColumns(['action', 'checkbox', 'total_bookings_count', 'published', 'pec_eligible', 'prices']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventSellableServiceView $model): QueryBuilder
    {
        $q = $model
            ->newQuery()
            ->where([
                'event_id' => $this->event->id,
            ])
            ->whereNull('deleted_at');
        if ($this->show_only_pec) {
            $q->whereNotNull("pec_eligible");
        }

        return $q;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-sellable');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('title')->title('Intitulé'),
            Column::make('service_date')->title('Date'),
            Column::make('stock_label')->title('Stock'),
            Column::make('total_bookings_count')->title('Stock commandé'),
            Column::make('available_label')->title('Stock restant réel'),
            Column::make('published')->title('En ligne'),
            Column::make('pec_eligible')->title('PEC'),
            Column::make('prices')->title('Prix'),
            Column::make('pec_paid_net')->title('Soldé HT PEC'),
            Column::make('pec_unpaid_net')->title('Non-soldé HT PEC'),
            Column::make('paid_net')->title('Soldé HT hors PEC'),
            Column::make('unpaid_net')->title('Non-solde HT hors PEC'),
            Column::make('congress_net')->title('CA HT Participants'),
            Column::make('industry_net')->title('CA HT Industriels'),
            Column::make('orators_net')->title('CA HT Intervenants'),
            Column::make('net_unassigned')->title('Non attribué'),
            Column::computed('action')->addClass('text-end')->title('Actions'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventSellableService_'.date('YmdHis');
    }
}
