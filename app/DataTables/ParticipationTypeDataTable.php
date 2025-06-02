<?php

namespace App\DataTables;

use App\Enum\ParticipantType;
use App\Models\ParticipationType;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ParticipationTypeDataTable extends DataTable
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
            ->addColumn('name', fn($row) => $row->name)
            ->addColumn('group', fn($row) => ParticipantType::translated($row->group))
            ->addColumn('default', fn($row) => $row->default ? view('components.dot', ['type' => 'success'])->render() : '')
            ->addColumn('action', function ($data) {
                return view('dictionnary.participation_type.datatable')->with([
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['entries', 'action', 'checkbox','default']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ParticipationType $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('participationtype');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('name')->title('Intitulé'),
            Column::make('group')->title('Groupe'),
            Column::make('default')->title('Par défaut')->className('text-center'),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Group_' . date('YmdHis');
    }
}
