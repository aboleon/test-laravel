<?php

namespace App\DataTables;

use App\DataTables\View\EventSellableSalesView;
use App\Enum\OrderClientType;
use App\Models\EventManager\Sellable;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventSellableSalesDataTable extends DataTable
{
    use Common;

    private string $locale;

    public function __construct(private readonly Sellable $sellable)
    {
        parent::__construct();
        $this->locale = app()->getLocale();
    }

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('status_html', function ($row) {
                return view('components.dot', ['type' => $row->cancelled != 1 ? 'success' : 'danger'])->render();
            })
            ->addColumn('actions', function ($row) {
                if ($row->dashboard_id) {

                    $type = $row->client_type == OrderClientType::GROUP->value ? OrderClientType::GROUP->value : OrderClientType::CONTACT->value;
                    return '<div class="justify-content-end d-flex"><a target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Dashboard" class="btn btn-sm btn-default mfw-edit-link" href="'.route("panel.manager.event.event_".$type.".edit", ['event' => $row->event_id, 'event_'.$type => $row->dashboard_id]).'">D</a>&nbsp;<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Commande"  target="_blank" class="btn btn-sm btn-secondary" href="'.route('panel.manager.event.orders.edit', ['event' => $row->event_id, 'order' => $row->order_id]).'">C</a></div>
                ';
                }
            })
            ->rawColumns(['status_html', 'actions']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventSellableSalesView $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->where('service_id', $this->sellable->id)
            ->orderByDesc('order_id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-sellable-stat', [
            'minifiedAjaxUrl' => route('panel.manager.event.sellable.sales.recap_ajax', ['event' => $this->sellable->event_id, 'sellable' => $this->sellable->id]),
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('order_id')->title('Commande'),
            Column::make('name')->title('Participant')->width(220),
            Column::make('quantity')->title('Quantité')->className('text-center'),
            Column::make('total_net')->title('HT')->className('text-center'),
            Column::make('total')->title('TTC')->className('text-center'),
            Column::make('total_pec')->title('PEC')->className('text-center'),
            Column::make('status_html')->title(' ')->orderable(false)->searchable(false),
            Column::make('status')->title('Statut')->className('text-center'),
            Column::make('actions')->title('')->width(200)->orderable(false)->searchable(false)->width(220),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventGrantStat_'.date('YmdHis');
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax(): JsonResponse
    {
        $query = null;
        if (method_exists($this, 'query')) {
            /** @var QueryBuilder|Builder|EloquentRelation $query */
            $query = app()->call([$this, 'query']);
            $query = $this->applyScopes($query);
        }

        /** @var DataTableAbstract $dataTable */
        // @phpstan-ignore-next-line
        $dataTable = app()->call([$this, 'dataTable'], compact('query'));

        if (is_callable($this->beforeCallback)) {
            app()->call($this->beforeCallback, compact('dataTable'));
        }

        if (is_callable($this->responseCallback)) {
            $data = new Collection($dataTable->toArray());

            $response = app()->call($this->responseCallback, compact('data'));

            return new JsonResponse($response);
        }

        return $dataTable->toJson();
    }
}
