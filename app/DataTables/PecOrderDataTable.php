<?php

namespace App\DataTables;

use App\DataTables\View\PecOrderView;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PecOrderDataTable extends DataTable
{
    use Common;

    private string $locale;

    public function __construct(private readonly Event $event, private readonly int $grantCount)
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
        $html = (new EloquentDataTable($query))
            ->addColumn('order_id', function ($row) {
                if (!$row->order_id) {
                    return null;
                }
                return '<a target="_blank" class="btn btn-sm btn-secondary" href="'.route('panel.manager.event.orders.edit', ['event' => $row->event_id, 'order' => $row->order_id]).'">'.$row->order_id.'</a>';
            })->addColumn('participant', function ($row) {
                return '<a target="_blank" href="'.route('panel.manager.event.event_contact.edit', [
                        'event' => $row->event_id,
                        'event_contact' => $row->event_contact_id
                    ]).'">'.$row->participant.'</a>';
            })
            ->orderColumn('order_id', 'order_id $1')
            ->orderColumn('participant', 'participant $1')
            ->orderColumn('total_net', "CAST(REPLACE(`total_net`, ',', '') AS DECIMAL(10,2)) $1")
            ->orderColumn('total_vat', "CAST(REPLACE(`total_vat`, ',', '') AS DECIMAL(10,2)) $1")
            ->orderColumn('total', "CAST(REPLACE(`total`, ',', '') AS DECIMAL(10,2)) $1");

        $rawColumns = ['order_id','participant'];

        if ($this->grantCount > 1) {
            $rawColumns[] = 'action';
            $html->addColumn('action', function ($data) {
                if ($this->grantCount > 1) {
                    return view('pec-orders.datatable.action')->with([
                        'data'   => $data,
                        'grants' => $this->event->grants->pluck('title', 'id'),
                    ])->render();
                }

                return '';
            });
        }

        return $html->rawColumns($rawColumns);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PecOrderView $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->where('event_id', $this->event->id)
            // TODO: A voir pourquoi ct nécessaire; ça a corrompu le fonctionnement prévu
            // introduit par Tony le 22 avril 2025, reverti par A.M le 10 mai 2025
            // https://github.com/Wagaia13/divine-id/commit/326062ca66950b2f06688890449d6c0303a1954d
                /*
            ->orWhere(function($query) {
                $query->where('event_id', $this->event->id)
                      ->whereExists(function($subquery) {
                          $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                                  ->from('event_deposits')
                                  ->whereColumn('event_deposits.event_contact_id', 'pec_order_view.event_contact_id')
                                  ->where('event_deposits.status', 'paid');
                      });
            })*/
            ;
            //->orderByDesc('order_id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('pec_order', [
            "orderBy" => 0,
            "orderByDirection" => "desc",
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        $cols = [
            Column::make('order_id')->title('Commande')->className('order_id')->orderable(true),
            Column::make('participant')->title('Participant')->className('participant'),
            Column::make('country')->title('Pays'),
            Column::make('domain')->title('Domaine'),
            Column::make('type')->title('Type PEC'),
            Column::make('shoppable')->title('Poste'),
            Column::make('total_net')->title('Montant HT'),
            Column::make('total_vat')->title('TVA'),
            Column::make('total')->title('Montant TTC'),
            Column::make('grant')->title('Grant'),
        ];

        if ($this->grantCount > 1) {
            $cols[] = Column::make('action')->title('Action');
        }

        return $cols;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PecOrder_'.date('YmdHis');
    }

}
