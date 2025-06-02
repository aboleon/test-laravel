<?php

namespace App\DataTables\Front;

use App\DataTables\BaseDataTable;
use App\Enum\OrderMarker;
use App\Enum\OrderOrigin;
use App\Models\EventContact;
use App\Models\Order;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class MyOrdersDataTableOld extends BaseDataTable
{

    use Common;

    protected $orderDetailsRouteName;


    public function __construct(
        protected EventContact $eventContact
    )
    {
        parent::__construct();
        $this->orderDetailsRouteName = 'front.event.orders.edit';
    }

    /**
     * Build the DataTable class.
     *
     * @param EloquentBuilder $query Results from query() method.
     */
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        $datatable = (new EloquentDataTable($query));

        return $datatable
            ->addColumn('action', fn($data) => $this->getActionContent($data))
            ->addColumn("created_at", fn($data) => $data->created_at->format(config('app.date_display_format') . ' - H\hi'))
            ->addColumn("total_ttc", fn($data) => ($data->total_net + $data->total_vat) . " €")
            ->addColumn("total_net", function ($data) {
                if ($data->order_invoice_id) {
                    return $data->total_net . " €";
                }
                return "";
            })
            ->addColumn("total_vat", function ($data) {
                if ($data->order_invoice_id) {
                    return $data->total_vat . " €";
                }
                return "";
            })
            ->addColumn("total_pec", fn($data) => $data->total_pec . " €")
            //--------------------------------------------
            // sort
            //--------------------------------------------
            ->orderColumn('total_ttc', '(total_net + total_vat) $1')
            ->orderColumn('created_at', 'created_at $1')
            //--------------------------------------------
            // search
            //--------------------------------------------
            ->filterColumn('created_at', function ($query, $keyword) {
                $sql = "created_at like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('uuid', function ($query, $keyword) {
                $sql = "uuid like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->rawColumns(['action']);
    }

    protected function getActionContent(Order $data)
    {
        return view('front.user.orders.datatable.action', [
            "data" => $data,
            "editDetailsRouteName" => $this->orderDetailsRouteName,
        ])->render();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Order $model): EloquentBuilder
    {
        return $model
            ->newQuery()
            ->select('orders.*', 'order_invoices.id as order_invoice_id')
            ->leftJoin('order_invoices', 'orders.id', '=', 'order_invoices.order_id')
            ->where('client_id', $this->eventContact->user_id)
            ->where('event_id', $this->eventContact->event_id)
            ->where(function ($query) {
                $query->where('total_net', '!=', 0)
                    ->orWhere('total_pec', '!=', 0);
            });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('datatable_front_my_orders', [
            'orderBy' => 0,
            'orderByDirection' => 'desc',
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('created_at')->title('Date'),
            Column::make('total_net')->title('Total HT'),
            Column::make('total_vat')->title('Total TVA'),
            Column::make('total_ttc')->title('Total TTC'),
            Column::make('total_pec')->title('Total PEC'),
            Column::computed('action')->addClass('text-end')->title('Actions')->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'FrontMyOrders_' . date('YmdHis');
    }
}
