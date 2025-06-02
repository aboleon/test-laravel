<?php

namespace App\DataTables;

use App\DataTables\View\HotelHistoryView;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HotelHistoryDataTable extends DataTable
{
    use Common;

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query));
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(HotelHistoryView $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('hotel');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('hotel')->title('Hôtel'),
            Column::make('locality')->title('Ville'),
            Column::make('country')->title('Pays'),
            Column::make('event')->title('Évènement'),
            Column::make('event_starts')->title('Début'),
            Column::make('event_ends')->title('Fin')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Hotel_' . date('YmdHis');
    }

}
