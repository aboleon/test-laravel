<?php

namespace App\DataTables;

use App\DataTables\View\EventProgramInterventionView;
use App\Helpers\DateHelper;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventProgramInterventionDataTable extends DataTable
{
    use Common;

    private array $locationTimes = [];
    private string $locale;


    public function __construct(
        private readonly Event $event,
        private                $sessionId = null,
    )
    {
        parent::__construct();
        $this->locale = app()->getLocale();
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowClass(function ($intervention) {
                if ($intervention->is_catering) {
                    return 'table-danger';
                } elseif ($intervention->is_placeholder) {
                    return 'table-success';
                }
                return !is_null($intervention->preferred_start_time) ? 'custom-intervention-row' : '';
            })
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '"></div>';
            })
            ->addColumn('duration_human', function ($data) {
                return DateHelper::convertMinutesToReadableDuration($data->duration);
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.program.intervention.datatable.action')->with([
                    'event' => $this->event,
                    'data' => $data,
                    'sessionId' => $this->sessionId,
                ])->render();
            })
            ->orderColumn('duration_human', 'duration $1')
            ->rawColumns(['action', 'checkbox']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventProgramInterventionView $model): QueryBuilder
    {
        $query = $model->newQuery();

        $query->where('event_id', $this->event->id);

        // Conditionally filter by sessionId
        if ($this->sessionId) {
            $query->where('event_program_session_id', $this->sessionId);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-intervention-datatable', [
            "orderBys" => [
                1 => "asc",
                2 => "asc",
            ],
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('container')->title("Conteneur")->searchable(false),
            Column::make('timings')->title("Horaires")->searchable(false),
            Column::make('session')->title("Session"),
            Column::make('name')->title("Nom"),
            Column::make('orators')->title("Intervenants"),
            Column::make('specificity')->title("Type intervention")->searchable(false),
            Column::make('duration_human')->title("Durée")->searchable(false),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventProgramIntervention_' . date('YmdHis');
    }
}
