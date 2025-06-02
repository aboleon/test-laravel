<?php

namespace App\DataTables;

use App\DataTables\View\EventGroupContactView;
use App\Models\Event;
use App\Models\EventManager\EventGroup;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class EventGroupContactDataTable extends BaseDataTable
{

    use Common;


    public function __construct(
        private readonly Event      $event,
        private readonly EventGroup $eventGroup,
    )
    {
        parent::__construct();
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
            ->addColumn('action', fn($data) => view('events.manager.event_group_contact.datatable.action', [
                "event" => $this->event,
                "eventGroup" => $this->eventGroup,
            ])->with(['data' => $data,])->render())
            ->rawColumns(['action']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventGroupContactView $model): EloquentBuilder
    {
        $eventGroupId = $this->eventGroup->id;
        return $model->newQuery()->where('event_group_id', $eventGroupId);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $params = [];
        $this->addBsTooltip($params);
        return $this->setHtml('event_group_dashboard_contact', [
            'minifiedAjaxUrl' => route('panel.manager.event.event_group.dashboard.contact', ['event' => $this->eventGroup->event->id, 'eventGroup' => $this->eventGroup->id]),
            'params' => $params,
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('first_name')->title('Prénom'),
            Column::make('last_name')->title('Nom'),
            Column::make('profile_function')->title('Fonction'),
            Column::make('email')->title('Email'),
            Column::make('locality')->title('Ville'),
            Column::make('country')->title('Pays'),
            Column::make('is_main_contact_display')->title('Est principal'),
            Column::computed('action')->addClass('text-end')->title('Actions')->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventGroupContact_' . date('YmdHis');
    }
}
