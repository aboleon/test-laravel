<?php

namespace App\DataTables;

use App\DataTables\View\EventContactDashboardSessionView;
use App\Enum\EventProgramParticipantStatus;
use App\Models\Event;
use App\Models\EventContact;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventContactDashboardSessionDataTable extends DataTable
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
            ->setRowClass(function ($row) {
                return match($row->status){
                    EventProgramParticipantStatus::VALIDATED->value => 'table-success',
                    EventProgramParticipantStatus::DENIED->value => 'table-danger',
                    default => ''
                };
            })
            ->addColumn('order_cancellation', function ($data)  {
                if (
                    $this->eventContact->order_cancellation &&
                    $this->eventContact->id === $data->event_contact_id
                ) {
                    return view('components.back.order-cancellation-pill')->render();
                }
                return "";
            })
            ->addColumn('action', function ($data) {
                return view('event-contact.dashboard.datatable.sessions-action')->with([
                    'session_id' => $data->session_id,
                    'event' => $this->event,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'order_cancellation']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventContactDashboardSessionView $model): QueryBuilder
    {
        $eventContactId = $this->eventContact->id;

        return $model->newQuery()
            ->select(
                'session_id',
                'event_contact_id',
                'status',
                'type',
                'session'
            )
            ->where('event_contact_id', $eventContactId);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('dashboard-moderators-sessions', [
            'minifiedAjaxUrl' => route('panel.manager.event.event_contact.dashboard.session', ['event' => $this->event->id, 'eventContact' => $this->eventContact->id]),
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('type')->title("Type"),
            Column::make('session')->title("Session"),
            Column::make('order_cancellation')->title("Annulation"),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventContactDashboardSession_' . date('YmdHis');
    }
}
