<?php

namespace App\DataTables;

use App\Models\Event;
use App\Models\EventManager\Sellable;
use App\Models\EventManager\Sellable\Choosable;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventChoosableDataTable extends DataTable
{
    use Common;

    public function __construct(private readonly Event $event)
    {
        parent::__construct();
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
            ->addColumn('title', function ($data) {
                return $data->title;
            })
            ->addColumn('published', function ($data) {
                return $data->isActive();
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.sellable.choosable.datatable.action')->with([
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'published']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Choosable $model): QueryBuilder
    {
        return $model->newQuery()->where('event_id', $this->event->id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-sellable-choosable');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('title')->title('Intitulé'),
            Column::make('published')->title('En ligne'),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventSellableChoosable_' . date('YmdHis');
    }
}
