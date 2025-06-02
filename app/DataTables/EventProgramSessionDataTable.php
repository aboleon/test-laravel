<?php

namespace App\DataTables;

use App\DataTables\View\EventProgramSessionView;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventProgramSessionDataTable extends DataTable
{
    use Common;


    public function __construct(
        private readonly Event $event,
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
                if($row->is_catering){
                    return 'table-danger';
                }
                elseif($row->is_placeholder){
                    return 'table-success';
                }
                return '';
            })
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '"></div>';
            })
            ->addColumn('interventions', function ($data) {
                $route = route('panel.manager.event.program.intervention.index', [
                    'event' => $this->event,
                    'session' => $data->id,
                ]);
                return '<a href="' . htmlspecialchars($route) . '"
                  class="btn btn-sm btn-success"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  data-bs-title="Interventions"
                  >
        <i class="bi bi-bounding-box"></i>
    </a>';
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.program.session.datatable.action')->with([
                    'data' => $data,
                    'event' => $this->event,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'interventions'])
            ->blacklist(['action', 'checkbox', 'interventions']); // code-notes 7334
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventProgramSessionView $model): QueryBuilder
    {
        return $model->newQuery()->where('event_id', $this->event->id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-session-datatable', [
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
            Column::make('date')->title("Date"),
            Column::make('timings')->title("Horaires"),
            Column::make('name')->title("Nom"),
            Column::make('sponsor')->title("Sponsor"),
            Column::make('moderators')->title("Modérateurs"),
            Column::make('place_room')->title("Salle"),
            Column::make('interventions')->title(__('programs.interventions')),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventProgramSession_' . date('YmdHis');
    }
}
