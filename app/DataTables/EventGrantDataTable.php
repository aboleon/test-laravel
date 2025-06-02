<?php

namespace App\DataTables;

use App\DataTables\View\EventGrantView;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventGrantDataTable extends DataTable
{
    use Common;

    private string $locale;

    public function __construct(private readonly Event $event)
    {
        parent::__construct();
        $this->locale = app()->getLocale();
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '"></div>';
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.grant.datatable.action')->with([
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'published']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventGrantView $model): QueryBuilder
    {
        return $model->newQuery()
            ->whereNull('deleted_at')
            ->where('event_id', $this->event->id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-grant');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('title')->title('Nom'),
            Column::make('contact')->title('Contact'),
            Column::make('comment')->title('Commentaire'),
            Column::make('amount_display')->title('Montant'),
            Column::make('amount_type')->title('Type'),
            Column::make('amount_used')->title('Utilisé'),
            Column::make('amount_remaining')->title('Restant'),
            Column::make('pec_fee_display')->title('Frais dossier'),
            Column::make('pax_avg')->title('Nombre PEC Moyen'),
            Column::make('pax_max')->title('Nombre PEC Max'),
            Column::make('active')->title('Activé'),
            Column::make('pax_count')->title('Allocations'),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventGrant_' . date('YmdHis');
    }
}
