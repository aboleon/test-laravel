<?php

namespace App\DataTables;

use App\DataTables\View\PlaceRoomView;
use App\Models\Place;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PlaceRoomDataTable extends DataTable
{
    use Common;

    public function __construct(public Place $place)
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
//            ->addColumn('name', function ($data) {
//                return $data->name;
//            })
//            ->addColumn('level', function ($data) {
//                return $data->level;
//            })
            ->addColumn('action', function ($data) {
                return view('places.rooms.datatable.action')->with([
                    'place' => $data->place,
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PlaceRoomView $model): QueryBuilder
    {
        return $model->newQuery()->where('place_id', $this->place->id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('placeroom');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('name')->title('Intitulé'),
            Column::make('level')->title(__('ui.rooms.level')),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PlaceRoom_' . date('YmdHis');
    }
}
