<?php

namespace App\DataTables\Front;

use App\DataTables\BaseDataTable;
use App\DataTables\View\FrontMyOrdersView;
use App\Enum\EventDepositStatus;
use App\Enum\OrderType;
use App\Models\EventContact;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Throwable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class MyOrdersDataTable extends BaseDataTable
{

    use Common;

    protected $orderDetailsRouteName;

    public function __construct(
        protected EventContact $eventContact,
    ) {
        parent::__construct();
        $this->orderDetailsRouteName = 'front.event.orders.edit';
    }

    /**
     * Build the DataTable class.
     *
     * @param  EloquentBuilder  $query  Results from query() method.
     */
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        $datatable = (new EloquentDataTable($query));

        return $datatable
            ->addColumn('action', fn($data) => $this->getActionContent($data))
            ->addColumn("date", fn($data) => $this->datetime($data->date))
            ->addColumn("total_ttc", fn($data) => $this->price($data->total_ttc))
            ->addColumn("total_net", fn($data) => $data->type == OrderType::GRANTDEPOSIT->value && $data->status != EventDepositStatus::BILLED->value ? '' : $this->price($data->total_net))
            ->addColumn("total_vat", fn($data) =>  $data->type == OrderType::GRANTDEPOSIT->value && $data->status != EventDepositStatus::BILLED->value ? '' : $this->price($data->total_vat))
            ->addColumn("total_pec", fn($data) => $data->type == OrderType::ORDER->value ? $this->price($data->total_pec) : '')
            ->orderColumn('date', 'date $1')
            ->setRowAttr([
                'data-order-type' => fn($data) => $data->type,
            ])
            ->rawColumns(['action']);
    }

    /**
     * @throws Throwable
     */
    protected function getActionContent(FrontMyOrdersView $data)
    {
        return view('front.user.orders.datatable.action', [
            "data"                 => $data,
            "editDetailsRouteName" => $this->orderDetailsRouteName,
        ])->render();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FrontMyOrdersView $model): EloquentBuilder
    {
        return $model
            ->newQuery()
            ->where('client_id', $this->eventContact->user_id)
            ->where('event_id', $this->eventContact->event_id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $params = [];
        $this->addResponsive($params);

        return $this->setHtml('datatable_front_my_orders', [
            'orderBy'          => 0,
            'orderByDirection' => 'desc',
            'params'           => $params,
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('order_id')->title('#'),
            Column::make('date')->title('Date'),
            Column::make('total_ttc')->title('Total TTC')->className('text-end'),
            Column::make('total_net')->title('Total HT')->className('text-end'),
            Column::make('total_vat')->title('Total TVA')->className('text-end'),
            Column::make('total_pec')->title('Total PEC')->className('text-end'),
            Column::computed('action')->addClass('text-end')->title('Actions')->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'FrontMyOrders_'.date('YmdHis');
    }

}
