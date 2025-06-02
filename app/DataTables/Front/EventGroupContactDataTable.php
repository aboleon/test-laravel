<?php

namespace App\DataTables\Front;

use App\DataTables\BaseDataTable;
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
            ->addColumn('action', fn($data) => view('front.user.group.members.datatable.action', [
                "event" => $this->event,
                "data" => $data,
            ])->render())
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
        $this->addResponsive($params);
        return $this->setHtml('datatable_front_event_group_contact', [
            'orderBy' => 0,
            'orderByDirection' => 'asc',
            'params' => $params,
        ]);
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::make('last_name')->title('Nom'),
            Column::make('first_name')->title('Prénom'),
            Column::make('locality')->title('Ville'),
            Column::make('country')->title('Pays'),
            Column::computed('action')->addClass('text-end')->title('Actions')->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'FrontEventGroupContact_' . date('YmdHis');
    }
}
