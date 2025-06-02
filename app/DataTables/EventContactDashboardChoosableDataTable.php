<?php

namespace App\DataTables;

use App\DataTables\View\EventContactDashboardChoosableView;
use App\Models\Event;
use App\Models\EventContact;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventContactDashboardChoosableDataTable extends DataTable
{
    use Common;

    public function __construct(
        private readonly Event        $event,
        private readonly EventContact $eventContact,
    )
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
            ->addColumn('order_cancellation', function ($data) {
                if (
                    $this->eventContact->order_cancellation &&
                    $this->eventContact->id === $data->event_contact_id
                ) {
                    return view('components.back.order-cancellation-pill')->render();
                }
                return "";
            })
            ->rawColumns(['order_cancellation']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventContactDashboardChoosableView $model): QueryBuilder
    {
        $eventContactId = $this->eventContact->id;
        return $model->newQuery()
            ->where("event_contact_id", $eventContactId)
            ->where('status', '!=', 'En attente');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('ec-dashboard-choosable-datatable', [
            'minifiedAjaxUrl' => route('panel.manager.event.event_contact.dashboard.choosable', ['event' => $this->event->id, 'eventContact' => $this->eventContact->id]),
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [

            Column::make('title')->title('Titre'),
            Column::make('date')->title("Date"),
            Column::make('status')->title("Statut"),
            Column::make('quantity')->title("Quantité"),
            Column::make('order_cancellation')->title("Annulation"),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventContactDashboardChoosable_' . date('YmdHis');
    }
}
