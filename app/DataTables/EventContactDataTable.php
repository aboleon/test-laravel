<?php

namespace App\DataTables;

use App\Accessors\EventContactAccessor;
use App\DataTables\View\EventContactView;
use App\Enum\ParticipantType;
use App\Enum\SavedSearches;
use App\Models\AdvancedSearchFilter;
use App\Models\Event;
use App\Traits\DataTables\Common;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventContactDataTable extends DataTable
{

    use Common;

    protected bool $withLastGrantNotNull = false;


    public function __construct(
        private readonly Event $event,
        private string $group = 'all',
        private ?string $withOrder = null,
        private $filteredIds = [],
    ) {
        parent::__construct();
    }

    /**
     * Build the DataTable class.
     *
     * @param  EloquentBuilder  $query  Results from query() method.
     */
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        $datatable = (new EloquentDataTable($query));

        return $datatable
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="'.$row->id.'"></div>';
            })
            ->addColumn('order_cancellation', function ($data) {
                if ($data->order_cancellation) {
                    return view('components.back.order-cancellation-pill')->render();
                }

                return "";
            })
            ->addColumn('pec_enabled_display', function ($row) {
                return ($row->pec_enabled ? '<span class="badge rounded-pill bg-'.($row->has_paid_grant_deposit ? 'success' : 'warning').'" style="width: 20px; height: 20px">&nbsp;</span>' : '');
            })
            ->addColumn('pec_eligible_display', function ($row) {
                return ($row->pec_eligible ? '<span class="badge rounded-pill bg-success" style="width: 20px; height: 20px">&nbsp;</span>' : '');
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.event_contact.datatable.action', [
                    "event"                => $this->event,
                    "group"                => $this->group,
                    "withLastGrantNotNull" => $this->withLastGrantNotNull,
                    "accessor"             => (new EventContactAccessor())->setEvent($this->event)->setAccount($data->eventContact->account)->setEventContact($data->eventContact),
                ])->with([
                    'data' => $data,
                ])->render();
            })
            ->orderColumn('order_cancellation', 'order_cancellation $1')
            ->rawColumns(['action', 'checkbox', 'order_cancellation', 'pec_status', 'pec_enabled_display', 'pec_eligible_display']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(): EloquentBuilder
    {
        $searchFilters = AdvancedSearchFilter::getFilters(SavedSearches::EVENT_CONTACTS->value);

        $groupId = request("group_id");
        $query   = EventContactView::query()
            ->where('event_id', $this->event->id)
            ->when($this->group !== 'all', function ($q) {
                if (in_array($this->group, ParticipantType::values())) {
                    return $q->where('participation_type_group', $this->group);
                }

                return $q;
            })
            ->when(null !== $groupId, function ($q) use ($groupId) {
                $groupId = intval($groupId);

                return $q->whereRaw("CONCAT(',', `group_ids`, ',') LIKE ?", ["%,$groupId,%"]);
            })
            ->when($this->withOrder == 'yes', function ($q) {
                return $q->where(
                    fn($where)
                        => $where
                        ->where("nb_orders", ">", 0)
                        ->orWhereNotNull('has_paid_service_deposit')
                        ->orWhereNotNull('has_paid_grant_deposit'),
                );
            })
            ->when($this->withOrder == 'no', function ($q) {
                return $q->where("nb_orders", 0);
            });

        if ( ! $this->filteredIds) {
            if ($searchFilters) {
                $query->join('advanced_searches', function ($join) {
                    $join
                        ->on('event_contact_view.id', '=', 'advanced_searches.id')
                        ->where('advanced_searches.auth_id', '=', auth()->id())
                        ->where('advanced_searches.type', '=', SavedSearches::EVENT_CONTACTS->value);
                });
            }
        } else {
            $query->whereIn('id', $this->filteredIds);
        }

        $query->select('event_contact_view.*');

        //$query->orderBy('created_at', 'desc');

        return $query->with(['eventContact.account']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('event_contact', [
            'orderBys' => [
                12 => 'desc',
            ],
        ])->drawCallback('function(){sendEventContactConfirmation.init();}');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        $columns = [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
        ];

        if (request('group') === 'all') {
            // might be useful
//            $columns = array_merge($columns, [
//                Column::make('participation_type_group_display')->title('Famille participant'),
//            ]);
            $columns = array_merge($columns, [
                Column::make('account_type_display')->title('Type de compte'),
            ]);
        }

        return array_merge($columns, [
            Column::make('domain')->title('Domaine'),
            Column::make('participation_type')->title('Participation'),
            Column::make('last_name')->title('Nom')->addClass('fw-bold'),
            Column::make('first_name')->title('Prénom'),
            Column::make('email')->title('E-mail'),
            Column::make('group')->title('Groupe'),
            Column::make('company_name')->title('Société'),
            Column::make('locality')->title('Ville'),
            Column::make('country')->title('Pays'),
            Column::make('fonction')->title('Profession'),
            Column::make('created_at')->title('Date Rattachement'),
            Column::make('nb_orders')->title('Commandes'),
            Column::make('order_cancellation')->title('Annulation commande'),
            Column::make('pec_enabled_display')->title('Pec Active')->className('text-center'),
            Column::make('pec_eligible_display')->title('Pec Eligible')->className('text-center'),
            Column::computed('action')->addClass('text-end')->title('Actions')->searchable(false),
        ]);
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Account_'.date('YmdHis');
    }
}

