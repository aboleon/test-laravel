<?php

namespace App\DataTables;

use App\DataTables\View\EventContactDashboardInterventionView;
use App\Enum\EventProgramParticipantStatus;
use App\Models\Event;
use App\Models\EventContact;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventContactDashboardInterventionDataTable extends DataTable
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
                return view('event-contact.dashboard.datatable.interventions-action')->with([
                    'intervention_id' => $data->intervention_id,
                    'event' => $this->event,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'order_cancellation']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventContactDashboardInterventionView $model): QueryBuilder
    {
        $eventContactId = $this->eventContact->id;

        return $model->newQuery()
            ->select(
                'event_contact_id',
                'status',
                'intervention_id',
                'session_id',
                'event_contact_id',
                'date_fr',
                'start_time',
                'end_time',
                'duration_formatted',
                'type',
                'title',
                'session'
            )
            ->where('event_contact_id', $eventContactId);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('dashboard-orators-interventions', [
            'minifiedAjaxUrl' => route('panel.manager.event.event_contact.dashboard.intervention', ['event' => $this->event->id, 'eventContact' => $this->eventContact->id]),
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [

            Column::make('date_fr')->title('Date'),
            Column::make('start_time')->title("Début"),
            Column::make('end_time')->title("Fin"),
            Column::make('duration_formatted')->title("Durée"),
            Column::make('type')->title("Type"),
            Column::make('title')->title("Titre"),
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
        return 'EventContactDashboardIntervention_' . date('YmdHis');
    }
}
