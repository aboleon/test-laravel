<?php

namespace App\DataTables;

use App\DataTables\View\EventGrantStatView;
use App\Enum\AmountType;
use App\Models\EventManager\Grant\Grant;
use App\Services\Pec\PecType;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Throwable;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventGrantRecapDataTable extends DataTable
{
    use Common;

    private string $locale;

    public function __construct(private readonly Grant $grant)
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
            ->addColumn('participant', function ($row) {
                try {
                    return '<a target="_blank" href="'.route('panel.manager.event.event_contact.edit', [
                        // TODO:: dans le cas de l'enregistrement grant transport on avait l'event_id manquant dans certains cas
                            'event'         => $row->event_id ?: $this->grant->event_id,
                            'event_contact' => $row->event_contact_id
                        ]).'">'.$row->participant.'</a>';
                } catch (Throwable $e) {
                    report($e);
                }
            })
            ->addColumn('order_id', function ($row) {
                if ($row->order_id) {
                    return '<a target="_blank" class="btn btn-sm btn-secondary" href="'.route('panel.manager.event.orders.edit', ['event' => $row->event_id, 'order' => $row->order_id]).'">'.$row->order_id.'</a>';
                }

                return '';
            })
            ->addColumn('total_net', function ($row) {
                if ($row->type_raw == PecType::TRANSPORT->value) {
                    return  $row->grant_type == 'ht' ? $row->total : $row->total_net;
                }
                return $row->total_net;
            })
            ->addColumn('total', function ($row) {
                if ($row->type_raw == PecType::TRANSPORT->value) {
                    return $row->grant_type == AmountType::TAX->value ? $row->total : '0.00';
                } else {
                    return $row->total;
                }
            })
            ->rawColumns(['order_id','participant']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventGrantStatView $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->where('grant_id', $this->grant->id)
            ->orderByDesc('order_id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-grant-stat', [
            'minifiedAjaxUrl' => route('panel.manager.event.grants.recap_ajax', ['event' => $this->grant->event_id, 'grant' => $this->grant->id]),
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('order_id')->title('Commande'),
            Column::make('participant')->title('Participant'),
            Column::make('locality')->title('Ville'),
            Column::make('country')->title('Pays'),
            Column::make('type')->title('Type PEC'),
            Column::make('shoppable')->title('Poste'),
            Column::make('total_net')->title('Montant HT'),
            Column::make('total_vat')->title('TVA'),
            Column::make('total')->title('Montant TTC'),
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
            /** @var EloquentBuilder|Builder|EloquentRelation $query */
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
