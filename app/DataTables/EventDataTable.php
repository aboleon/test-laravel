<?php

namespace App\DataTables;

use App\DataTables\View\EventView;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventDataTable extends DataTable
{
    use Common;

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
                return view('events.datatable.action')->with([
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox','published']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventView $model): QueryBuilder
    {
        return $model->newQuery()->whereNull('deleted_at');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('event');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('name')->title('Intitulé'),
            Column::make('subname')->title('Acronyme'),
            Column::make('type')->title('Type'),
            Column::make('parent')->title(__('events.parent')),
            Column::make('admin')->title(__('events.admin')),
            Column::make('starts')->title(__('ui.start')),
            Column::make('ends')->title(__('ui.end')),
            Column::make('participants_count')->title('Participants')->addClass('text-center'),
            Column::make('codecb')->title(__('events.cbcode')),
            Column::make('published')->title(__('mfw.published.online')),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Event_' . date('YmdHis');
    }
}
