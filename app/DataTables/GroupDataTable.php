<?php

namespace App\DataTables;

use App\DataTables\View\GroupView;
use App\Enum\SavedSearches;
use App\Helpers\AdvancedSearch\ContactAdvancedSearchHelper;
use App\Helpers\AdvancedSearch\GroupAdvancedSearchHelper;
use App\Models\AdvancedSearchFilter;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class GroupDataTable extends DataTable
{
    use Common;

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
            ->addColumn('events', function ($data) {
                return view('groups.datatable.events')->with([
                    'data' => $data,
                ])->render();
            })
            ->addColumn('action', function ($data) {
                return view('groups.datatable.action')->with([
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'events']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(GroupView $model): QueryBuilder
    {
        $searchFilters = AdvancedSearchFilter::getFilters(SavedSearches::GROUPS->value);

        $query = $model->newQuery()->whereNull('deleted_at');

        if ($searchFilters) {
            $query->join('advanced_searches', function ($join) {
                $join
                    ->on('group_view.id', '=', 'advanced_searches.id')
                    ->where('advanced_searches.auth_id', '=', auth()->id())
                    ->where('advanced_searches.type', '=', SavedSearches::GROUPS->value);
            });
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('group');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->width('50'),
            Column::make('name')->title('Intitulé'),
            Column::make('company')->title('Raison sociale'),
            Column::make('phone')->title('Téléphone'),
            Column::make('country')->title('Pays'),
            //Column::make('events')->addClass('unclickable')->title('Évènements')->searchable(false)->orderable(false),
            Column::computed('action')->addClass('text-end')->title('Actions'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Group_'.date('YmdHis');
    }
}
