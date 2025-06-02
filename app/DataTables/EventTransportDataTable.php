<?php

namespace App\DataTables;

use App\Accessors\Chronos;
use App\Enum\DesiredTransportManagement;
use App\Enum\ParticipantType;
use App\Models\Event;
use App\Models\EventManager\Transport\EventTransport;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use MetaFramework\Accessors\Prices;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventTransportDataTable extends DataTable
{
    use Common;

    private string $notAvailableText = "N/A";
    private bool $filterByDesiredManagement = true;
    private ?int $eventContactId = null;
    private ?string $route = null;
    protected ?string $htmlId = null;
    protected ?array $htmlParams = null;

    protected ?string $target = null;


    public function __construct(
        private readonly Event $event,
        private readonly bool $isDesired = true,
    ) {
        parent::__construct();
    }

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="'.$row->id.'"></div>';
            })
            ->addColumn('pec_authorized', fn($data) => $data->pec_authorized
                ? view('components.enabled-mark', ['enabled' => $data->pec_authorized,'label'=>'Pec'])
                : ''
            )
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('pec', function ($data) {
                return $data->pec;
            })
            ->addColumn('participation_type', function ($data) {
                return $data->eventContact->participationType ?
                    ParticipantType::translated($data->eventContact->participationType->group) : "Non défini";
            })
            ->addColumn('departure_step', function ($data) {
                return $data->departureStep->name ?? $this->notAvailableText;
            })
            ->addColumn('departure_transport_type', function ($data) {
                return $data->departureTransportType->name ?? $this->notAvailableText;
            })
            ->addColumn('departure_start_date', function ($data) {
                return $data->departure_start_date ? Chronos::formatDate($data->departure_start_date) : $this->notAvailableText;
            })
            ->addColumn('departure_end_time', function ($data) {
                return $data->departure_end_time ? Chronos::formatTime($data->departure_end_time) : $this->notAvailableText;
            })
            ->addColumn('departure_start_location', function ($data) {
                return $data->departure_start_location ?? $this->notAvailableText;
            })
            ->addColumn('departure_end_location', function ($data) {
                return $data->departure_end_location ?? $this->notAvailableText;
            })
            ->addColumn('return_step', function ($data) {
                return $data->returnStep->name ?? $this->notAvailableText;
            })
            ->addColumn('return_transport_type', function ($data) {
                return $data->returnTransportType->name ?? $this->notAvailableText;
            })
            ->addColumn('return_start_date', function ($data) {
                return $data->return_start_date ? Chronos::formatDate($data->return_start_date) : $this->notAvailableText;
            })
            ->addColumn('return_start_time', function ($data) {
                return $data->return_start_time ? Chronos::formatTime($data->return_start_time) : $this->notAvailableText;
            })
            ->addColumn('return_start_location', function ($data) {
                return $data->return_start_location ?? $this->notAvailableText;
            })
            ->addColumn('return_end_location', function ($data) {
                return $data->return_end_location ?? $this->notAvailableText;
            })
            ->addColumn('transfer', function ($data) {
                return $data->transfer_requested ? __('ui.yes') : __('ui.no');
            })
            ->addColumn('price_before_tax', function ($data) {
                return $data->amount_ht ?: $this->notAvailableText;
            })
            ->addColumn('price_after_tax', function ($data) {
                return $data->amount_tax ?: $this->notAvailableText;
            })
            ->addColumn('max_reimbursement_amount', function ($data) {
                return $data->max_reimbursement_amount ?: $this->notAvailableText;
            })
            ->addColumn('has_documents', function ($data) {
                return $data->has_documents ? __('ui.yes') : __('ui.no');
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.transport.desired_datatable.action')->with([
                    'data'   => $data,
                    'event'  => $this->event,
                    'target' => $this->target,
                ])->render();
            })

            //--------------------------------------------
            // sort
            //--------------------------------------------
//            ->orderColumn('date', 'event_program_days.datetime_start $1')
            ->orderColumn('name', 'name $1')
            ->orderColumn('departure_start_location', 'departure_start_location $1')
            ->orderColumn('departure_end_location', 'departure_end_location $1')
            ->orderColumn('return_start_location', 'return_start_location $1')
            ->orderColumn('return_end_location', 'return_end_location $1')
            ->orderColumn('participation_type', 'participation_type $1')
            //--------------------------------------------
            // search
            //--------------------------------------------
            ->filterColumn('name', function ($query, $keyword) {
                $sql = "CONCAT(users.first_name, ' ', users.last_name) like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('departure_start_location', function ($query, $keyword) {
                $sql = "departure_start_location like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('departure_end_location', function ($query, $keyword) {
                $sql = "departure_end_location like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('return_start_location', function ($query, $keyword) {
                $sql = "return_start_location like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('return_end_location', function ($query, $keyword) {
                $sql = "return_end_location like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->rawColumns(['pec_authorized','action', 'checkbox']);
//            ->blacklist(['action', 'checkbox', 'interventions']); // code-notes 7334
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventTransport $model): QueryBuilder
    {
        $documentSubquery = DB::table('mediaclass')
            ->whereColumn('event_transports.id', 'model_id')
            ->whereIn('group', ['transport_return', 'transport_departure'])
            ->limit(1)
            ->select(DB::raw(1));


        $query = $model
            ->newQuery()
            ->where('events_contacts.event_id', '=', $this->event->id)
            ->with(['departureTransportType', 'departureStep', 'returnTransportType', 'returnStep'])
            ->with('eventContact.participationType')
            ->select(
                "event_transports.*",
                "events_contacts.order_cancellation",
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                'participation_types.name as participation_type',
                DB::raw("CASE WHEN EXISTS ({$documentSubquery->toSql()}) THEN '1' ELSE '0' END as has_documents"),
                DB::raw("
            CASE
                WHEN events_contacts.pec_enabled IS NOT NULL
                AND events_contacts.is_pec_eligible IS NOT NULL
                AND (events_contacts.grant_deposit_not_needed = 1 OR EXISTS (
                    SELECT 1 FROM event_deposits
                    WHERE event_deposits.event_contact_id = events_contacts.id
                    AND event_deposits.status IN ('paid', 'billed')
                    AND event_deposits.shoppable_type = 'grantdeposit'
                ))
                THEN 1
                ELSE NULL
            END AS pec_authorized
        "),
                DB::raw("
            CASE
                WHEN events_contacts.pec_enabled IS NOT NULL
                AND (
                    events_contacts.pec_enabled IS NOT NULL
                    AND events_contacts.is_pec_eligible IS NOT NULL
                    AND (events_contacts.grant_deposit_not_needed = 1 OR EXISTS (
                        SELECT 1 FROM event_deposits
                        WHERE event_deposits.event_contact_id = events_contacts.id
                        AND event_deposits.status IN ('paid', 'billed')
                        AND event_deposits.shoppable_type = 'grantdeposit'
                    ))
                ) IS NULL
                THEN 1
                ELSE NULL
            END AS pec_enabled
        "),
                DB::raw("
            FORMAT(
                (SELECT unit_price FROM pec_distribution
                WHERE pec_distribution.event_contact_id = events_contacts.id
                LIMIT 1) / 100, 2
            ) AS pec
        "),
                DB::raw("
            FORMAT(event_transports.max_reimbursement / 100, 2) AS max_reimbursement_amount
        "),
                DB::raw("
            FORMAT(event_transports.price_before_tax / 100, 2) AS amount_ht
        "),
                DB::raw("
            FORMAT(event_transports.price_after_tax / 100, 2) AS amount_tax
        ")
            )
            ->join('events_contacts', 'events_contacts.id', '=', 'event_transports.events_contacts_id')
            ->join('users', 'users.id', '=', 'events_contacts.user_id')
            ->join('participation_types', 'participation_types.id', '=', 'events_contacts.participation_type_id', 'left')
            ->leftJoin('event_deposits', function ($join) {
                $join->on('event_deposits.event_contact_id', '=', 'events_contacts.id')
                    ->whereIn('event_deposits.status', ['paid', 'billed'])
                    ->where('event_deposits.shoppable_type', 'grantdeposit');
            });



        // Bindings for the subquery
        $query->addBinding($documentSubquery->getBindings(), 'select');

        if ($this->filterByDesiredManagement) {
            if ($this->isDesired) {
                $query->where('event_transports.desired_management', '=', DesiredTransportManagement::DIVINE);
            } else {
                $query->where('event_transports.desired_management', '!=', DesiredTransportManagement::DIVINE);
            }
        }

        if ($this->eventContactId) {
            $query->where('events_contacts.id', '=', $this->eventContactId);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        // Determine the ID for the table
        $id = $this->htmlId ?? ($this->isDesired ? 'eventmanager-transport-desired' : 'eventmanager-transport-undesired');

        // Determine the route
        $route = $this->route;
        if ( ! $route) {
            $routeName = $this->isDesired ? 'panel.manager.event.transport.desired_data' : 'panel.manager.event.transport.undesired_data';
            $route     = route($routeName, $this->event);
        }

        // Prepare parameters
        if ( ! $this->htmlParams) {
            $params = [];
            if ( ! $this->isDesired) {
                $params['rowCallback'] = <<<JS
                    function(row, data, index){
                        if('participant' === data.desired_management){
                            $(row).addClass('row-participant');
                        }
                    }
                    JS;
            }
        } else {
            $params = $this->htmlParams;
        }

        // Call the setHtml method from the Common trait
        return $this->setHtml($id, [
            'minifiedAjaxUrl' => $route,
            'params'          => $params,
        ]);
    }


    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        $columns = [];

        $useCheckbox          = true;
        $usePec               = true;
        $useParticipationType = true;
        $useTransportType     = true;
        $useTransfer          = true;

        if ('eventContactDashboard' === $this->target) {
            $useCheckbox          = false;
            $usePec               = false;
            $useParticipationType = false;
            $useTransportType     = false;
            $useTransfer          = false;
        }


//        $columns[] = Column::computed('DT_RowIndex')->title('#')->orderable(false)->searchable(false);

        if ($useCheckbox) {
            $columns[] = Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50');
        }

        $columns[] = Column::make('pec_authorized')->title(' ')->orderable(false);
        $columns[] = Column::make('name')->title(__('transport.name'))->searchable();

        if ($usePec) {
            $columns[] = Column::make('pec')->title(__('transport.pec'));
        }

        if ($useParticipationType) {
            $columns[] = Column::make('participation_type')->title(__('transport.participation_type'));
        }

        $columns[] = Column::make('departure_step')->title(__('transport.departure_step'));

        if ($useTransportType) {
            $columns[] = Column::make('departure_transport_type')->title(__('transport.departure_transport_type'));
        }
        $columns[] = Column::make('departure_start_date')->title(__('transport.departure_start_date'));
        $columns[] = Column::make('departure_end_time')->title(__('transport.departure_start_time'));
        $columns[] = Column::make('departure_start_location')->title(__('transport.departure_start_location'));
        $columns[] = Column::make('departure_end_location')->title(__('transport.departure_end_location'));

        $columns[] = Column::make('return_step')->title(__('transport.return_step'));

        if ($useTransportType) {
            $columns[] = Column::make('return_transport_type')->title(__('transport.return_transport_type'));
        }

        $columns[] = Column::make('return_start_date')->title(__('transport.return_start_date'));
        $columns[] = Column::make('return_start_time')->title(__('transport.return_start_time'));
        $columns[] = Column::make('return_start_location')->title(__('transport.return_start_location'));
        $columns[] = Column::make('return_end_location')->title(__('transport.return_end_location'));

        // other columns

        if ($useTransfer) {
            $columns[] = Column::make('transfer')->title(__('transport.transfer'));
        }

        $columns[] = Column::make('price_before_tax')->title(__('transport.price_before_tax'));
        $columns[] = Column::make('price_after_tax')->title(__('transport.price_after_tax'));
        $columns[] = Column::make('max_reimbursement_amount')->title(__('transport.max_reimbursement_amount'));
        $columns[] = Column::make('has_documents')->title(__('transport.has_documents'));

        $columns[] = Column::computed('action')->addClass('text-end')->title(__('transport.actions'));


        return $columns;
    }

    public function setFilterByDesiredManagement(bool $filterByDesiredManagement): self
    {
        $this->filterByDesiredManagement = $filterByDesiredManagement;

        return $this;
    }

    public function setEventContactId($eventContactId): self
    {
        $this->eventContactId = $eventContactId;

        return $this;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventTransport_'.date('YmdHis');
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function ttcPrice($price)
    {
        return Prices::readableFormat($price)." TTC";
    }
}
