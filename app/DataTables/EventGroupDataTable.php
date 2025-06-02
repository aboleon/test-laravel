<?php

namespace App\DataTables;

use App\DataTables\View\EventGroupView;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventGroupDataTable extends DataTable
{

    use Common;


    public function __construct(
        private Event $event,
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
            ->addColumn('checkbox', function ($row) {
                return $row->orders_count ? '' : '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '"></div>';
            })
            ->addColumn('main_contact_email', function ($row) {
                return '<a onclick="event.stopPropagation();" href="mailto:' . $row->main_contact_email . '">' . $row->main_contact_email . '</a>';
            })
            ->addColumn('participants_count', function ($row) {
                return '<a onclick="event.stopPropagation();" href="' . route("panel.manager.event.event_contact.index", [
                        "event" => $this->event,
                        "group" => "all",
                        "group_id" => $row->group_id,
                    ]) . '">' . $row->participants_count . '</a>';
            })
            ->addColumn('action', fn($data) => view('events.manager.event_group.datatable.action', [
                "event" => $this->event,
            ])->with([
                'data' => $data,
            ])->render())
            ->rawColumns(['action', 'checkbox', 'main_contact_email', 'participants_count']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(): EloquentBuilder
    {
        return EventGroupView::query()
            ->where('event_id', $this->event->id)->orderBy('event_group_created_at', 'desc');

    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('event_groups');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('group_name')->title('Nom'),
            Column::make('main_contact_name')->title('Contact'),
            Column::make('group_company')->title('RS'),
            Column::make('comment')->title('Commentaire'),
            Column::make('main_contact_email')->title('Email'),
            Column::make('main_contact_phone')->title('Tél.'),
            Column::make('main_contact_country')->title('Pays'),
            Column::make('participants_count')->title('Part.'),
            Column::make('event_group_created_at')->title('Date'),
            Column::computed('action')->addClass('text-end')->title('Actions')->searchable(false)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventGroup_' . date('YmdHis');
    }
}
