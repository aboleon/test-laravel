<?php

namespace App\DataTables;

use App\DataTables\View\EventDepositView;
use App\Enum\EventDepositStatus;
use App\Enum\OrderType;
use App\Models\Event;
use App\Printers\Event\Deposit;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventDepositDataTable extends DataTable
{
    use Common;

    public function __construct(public Event $event, private readonly ?string $status = null)
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
            ->addColumn('order_id', function ($row) {
                return '<a href="' . route('panel.manager.event.orders.edit', [
                        'event' => $row->event_id,
                        'order' => $row->order_id,
                    ]) . '" class="btn btn-link">' . $row['order_id'] . '</a>';

            })
            ->addColumn("beneficiary_name", function ($row) {
                if (!$row['beneficiary_name']) {
                    return "N/A";
                }
                return '<a href="' . route('panel.manager.event.event_contact.edit', [
                        'event' => $row->event_id,
                        'event_contact' => $row->event_contact_id,
                    ]) . '" class="btn btn-link">' . $row['beneficiary_name'] . '</a>';
            })
            ->addColumn("total_ttc", function ($row) {
                return Deposit::printTotalTtc($row);
            })
            ->addColumn("total_net", function ($row) {
                return Deposit::printTotalNet($row);
            })
            ->addColumn("status", function ($row) {
                return Deposit::printStatus($row);
            })
            ->addColumn("is_attending_expired", function ($row) {
                if ($row->is_attending_expired) {
                    return '<div class="badge btn-danger rounded-circle">&nbsp;</div>';
                }
                return "";
            })
            ->addColumn('action', function ($data) {
                return view('event-deposits.datatable.action')->with([
                    'data' => $data,
                ])->render();
            })
            ->orderColumn('total_ttc', 'total_ttc $1')
            ->orderColumn('total_net', 'total_net $1')
            ->orderColumn('order_id', 'order_id $1')
            ->orderColumn('service_name', 'service_name $1')
            ->orderColumn('beneficiary_name', 'beneficiary_name $1')
            ->orderColumn('status', 'status $1')
            ->orderColumn('is_attending_expired', 'is_attending_expired $1')
            ->rawColumns(['action', 'checkbox', 'order_id', "service_name", "beneficiary_name", "status", 'is_attending_expired']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventDepositView $model): QueryBuilder
    {
        $query  = $model->newQuery()
            ->where('event_id', $this->event->id);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('order_id','desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('order_sellable_deposit');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('type')->title('Type'),
            Column::make('order_id')->title('N° Commande'),
            Column::make('date_fr')->title('Date'),
            Column::make('shoppable_label')->title('Désignation'),
            Column::make('total_ttc')->title('Total TTC'),
            Column::make('total_net')->title('Total HT'),
            Column::make('beneficiary_name')->title('Contact'),
            Column::make('status')->title('Statut'),
            Column::make('is_attending_expired')->title('Alerte Présence'),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'OrderSellableDeposit_' . date('YmdHis');
    }
}
