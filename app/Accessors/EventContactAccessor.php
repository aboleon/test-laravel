<?php

namespace App\Accessors;

use App\Accessors\EventManager\Grant\GrantAccessor;
use App\Accessors\Front\Sellable\Accommodation;
use App\Accessors\Front\Sellable\Service;
use App\Actions\EventManager\GrantActions;
use App\DataTables\View\EventContactView;
use App\Enum\{ApprovalResponseStatus, EventDepositStatus, OrderCartType, OrderClientType, OrderMarker, OrderSource, OrderStatus, ParticipantType};
use App\Helpers\DateHelper;
use App\Models\{Account, Event, EventContact, EventManager\EventGroup\EventGroupContact, Order, Order\EventDeposit};
use App\Services\Grants\ParsedGrant;
use App\Services\Pec\PecParser;
use App\Services\Pec\PecType;
use App\Traits\Models\AccountModelTrait;
use App\Traits\Models\EventContactModelTrait;
use App\Traits\Models\EventModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MetaFramework\Accessors\VatAccessor;
use Throwable;

class EventContactAccessor
{
    use AccountModelTrait;
    use EventModelTrait;
    use EventContactModelTrait;

    private ?array $data = null;
    private bool $calculated = false;

    private ?Builder $ordersQuery = null;
    private array $bookedServices = [];
    private ?bool $hasAnyTransportPec = null;
    private ?bool $isPecAuthorized = null;
    private EloquentCollection|null $transportPec = null;
    private ?bool $hasOrdersOrGroupOrders = null;
    private ?int $hasAnyOrders = null;
    private ?Collection $eventGroups = null;

    public function __construct(null|int|Event $event = null, null|int|Account $account = null)
    {
        $this->setEvent($event);
        $this->setAccount($account);

        if ($this->eventContact === null && $this->account && $this->event) {
            $this->setEventContactFromEventAccount($this->event, $this->account);
        }
    }

    // Getters
    public function get(string $key): mixed
    {
        return $this->fetchData()[$key] ?? null;
    }

    private function getEvent(): ?Event
    {
        if ( ! $this->event) {
            $this->event = $this->eventContact->event;
        }

        return $this->event;
    }

    public function fetchData(): array
    {
        if ($this->data === null) {
            $this->generateData();
        }

        return $this->data;
    }

    public function getModel(): EventContact
    {
        return $this->eventContact->load('user');
    }

    public function hasSomething(): bool
    {
        return (bool)collect($this->fetchData())->reject(fn($item) => $item['total'] == 0)->count();
    }

    public function hasNothing(): bool
    {
        return ! $this->hasSomething();
    }

    # PEC methods

    public function hasPaidGrantDeposit(): bool
    {
        if ($this->isExemptGrantFromDeposit()) {
            return true;
        }

        if ( ! $this->eventContact->grantDeposit) {
            return false;
        }

        return in_array($this->eventContact->grantDeposit->status, EventDepositStatus::paid());
    }

    public function isPecAuthorized(): bool
    {
        if ($this->isPecAuthorized === null) {
            $this->isPecAuthorized = $this->eventContact->is_pec_eligible && $this->eventContact->pec_enabled && $this->hasPaidGrantDeposit();
        }

        return $this->isPecAuthorized;
    }

    public function isExemptGrantFromDeposit(): bool
    {
        return (bool)$this->eventContact->grant_deposit_not_needed;
    }

    public function getPendingGrantDeposit(): ?EventDeposit
    {
        if ( ! $this->eventContact->grantDeposit) {
            return null;
        }

        if ( ! in_array($this->eventContact->grantDeposit->status, EventDepositStatus::paid())) {
            return $this->eventContact->grantDeposit;
        }

        return null;
    }

    public function getPayableGrantDeposit(): ?EventDeposit
    {
        $deposit = $this->getPendingGrantDeposit();

        if ($deposit) {
            return $deposit;
        }
        $grant = $this->getPreferredGrant();

        if ($grant) {
            try {
                $grantData    = (new GrantAccessor())->parsedGrandData($grant);
                $grantActions = new GrantActions();
                $grantActions->makeGrantDeposit($this->eventContact, $grantData, state: EventDepositStatus::TEMP->value);

                return $grantActions->getDeposit();
            } catch (Throwable $e) {
                Log::error("Impossible d'enregistrer un EventDeposit");
                Log::error($e->getMessage());

                return null;
            }
        }

        return null;
    }


    /**
     * Retrouve le premier Grant dispo pour le contact
     *
     * @return ParsedGrant|null
     */
    public function getPreferredGrant(): ?ParsedGrant
    {
        $event     = $this->getEvent();
        $pecParser = new PecParser($event, collect()->push($this->eventContact));
        $pecParser->calculate();

        return $pecParser->getPreferedGrantFor($this->eventContact);
    }

    public function getPreferedGrantNormalizedData(): array
    {
        $grant = $this->getPreferredGrant();

        if ( ! $grant) {
            $this->responseError("Aucun grant n'est actuellement disponible pour ce participant. Par conséquent la PEC ne peut pas être activée.");

            return $this->fetchResponse();
        }

        $vat_id = $grant->event_pec_config['waiver_fees_vat_id'] ?? VatAccessor::defaultId();
        $title  = $grant->config['title'];

        if (empty($grant->event_pec_config['waiver_fees_vat_id'])) {
            $this->responseWarning(
                "La TVA pour le grant ".$title."  n'est pas définie."
                .(empty($grant->event_pec_config['waiver_fees_vat_id']) && $vat_id ? " Le taux de TVA par défaut sera utilisé." : "Impossible de continue"),
                $vat_id == 0,
            );
            if ($this->hasErrors()) {
                return $this->fetchResponse();
            }
        }

        $depositFee = $grant->config['deposit_fee'] ?? $grant->event_pec_config['waiver_fees'];

        if (empty($depositFee)) {
            $this->responseError("Le montant de la caution pour le grant ".$title." n'est pas définie");

            return $this->fetchResponse();
        }

        $data = [
            'id'      => $grant->id,
            'vat_id'  => $vat_id,
            'title'   => $title ?? 'Sans nom',
            'deposit' => $depositFee,
        ];

        $this->responseElement('grant', $data);

        $response = $this->fetchResponse();

        $this->reset();


        return $response;
    }


    /**
     * Retourne les ID des services réservés PEC et leur quantité
     *
     * @return array
     */
    public function getBookedPecServices(): array
    {
        return $this->getPecAcquiredItems(type: PecType::SERVICE->value)->pluck(
            'quantity',
            'shoppable_id',
        )->toArray();
    }

    public function getPecAcquiredItems(string $type = '')
    {
        $items = $this->eventContact->pecDistributions;
        if ($type) {
            return $items->where('type', $type);
        }

        return $items;
    }

    public function hasAnyPecAccommodation(): bool
    {
        return $this->getPecAcquiredItems(type: PecType::ACCOMMODATION->value)->count() > 0;
    }

    public function getPecAccommodationDates(): array
    {
        $items = $this->getPecAcquiredItems(type: PecType::ACCOMMODATION->value);

        if ($items->isEmpty()) {
            return [];
        }

        return Order\Cart\AccommodationCart::whereIn('order_id', $items->pluck('order_id'))->pluck('date')->unique()->sort()->toArray();
    }

    public function transportPec(): EloquentCollection
    {
        if ($this->transportPec === null) {
            $this->transportPec = $this->eventContact->grantStats->where('type_row', '=', PecType::TRANSPORT->value);
        }

        return $this->transportPec;
    }


    public function hasAnyTransportPec(): bool
    {
        if ($this->hasAnyTransportPec === null) {
            $this->hasAnyTransportPec = $this->transportPec()->isNotEmpty();
        };

        return $this->hasAnyTransportPec;
    }

    # --end PEC methods


    # Computations
    public function calculate(): self
    {
        if ( ! $this->calculated && $this->eventContact) {
            // Queries
            /*
            $this->ordersQuery();
            $this->fetchTransport();
            $this->fetchIntervetions();
            $this->fetchSessions();
            $this->fetchCustomServuces();
            */
            $this->loadCounters();
            $this->calculated = true;
        }

        if ( ! $this->eventContact) {
            $this->responseError("Le Contact n'a pas pu être identifié.");
        }

        return $this;
    }


    public function isDeletable(): bool
    {
        return ! $this->hasSomething();
    }

    public function generateData(): self
    {
        $this->calculate();

        $this->data = [
            'orders'        => [
                'total' => $this->hasAnyOrders(),
            ],
            'transport'     => [
                'total' => $this->eventContact->transport_count,
            ],
            'interventions' => [
                'total' => $this->eventContact->program_intervention_orators_count,
            ],
            'sessions'      => [
                'total' => $this->eventContact->program_session_moderators_count,
            ],
            'choosables'    => [
                'total' => (int)$this->eventContact
                    ->choosables()->where('status', ApprovalResponseStatus::VALIDATED->value)
                    ->exists(),
            ],
        ];

        return $this;
    }

    public function hasAnyOrders(): int
    {
        if ($this->hasAnyOrders === null) {
            $this->hasAnyOrders = $this->eventContact
                ->orders()
                ->where(function ($query) {
                    $query
                        ->where('event_id', $this->eventContact->event_id)
                        ->whereDoesntHave('deposits')
                        ->orWhereHas('deposits', function ($q) {
                            $q->whereNotIn('status', EventDepositStatus::deletableStates());
                        });
                })
                ->count();
        }

        return $this->hasAnyOrders;
    }


    private function loadCounters(): void
    {
        $this->eventContact->loadCount(
            ['choosables', 'programSessionModerators', 'programInterventionOrators', 'transport', 'orders'],
        );
    }

    public function isEligibleForTransfer(): bool
    {
        if ( ! $this->event->transfert) {
            return false;
        }
        if ($this->isPecAuthorized()) {
            return in_array('pec', (array)$this->event->transfert);
        } else {
            if (in_array(ParticipantType::ORATOR->value, (array)$this->event->transfert) && $this->isOrator()) {
                return true;
            }
            if (in_array(ParticipantType::CONGRESS->value, (array)$this->event->transfert) && $this->isParticipant()) {
                return true;
            }
        }

        return false;
    }

    public static function selectableByEvent(Event $event): array
    {
        $eventContacts = EventContact::with('user')
            ->where('event_id', $event->id)
            ->get();


        $sortedEventContacts = $eventContacts->sortBy(function ($contact) {
            return $contact->user?->last_name;
        });


        $selectable = $sortedEventContacts->mapWithKeys(function ($contact) {
            return [$contact->id => $contact->user?->fullName()];
        });

        return $selectable->toArray();
    }

    public static function getSearchResultsByEventId(int $eventId, string $searchTerm = "", array $options = []): array
    {
        $mode                   = $options['mode'] ?? 'bs';
        $valueKey               = $options['valueKey'] ?? "value";
        $textKey                = $options['textKey'] ?? "text";
        $pluck                  = $options['pluck'] ?? false;
        $participantMapCallback = $options['participantMapCallback'] ?? null;

        $exclude_group = $options['exclude_group'] ?? null;

        $query = EventContactView::query()
            ->where('event_id', $eventId)
            ->when(
                $searchTerm,
                fn($q) => $q->where(fn($where)
                    => $where
                    ->where('first_name', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$searchTerm.'%')),
            );

        if ($exclude_group) {
            $query->whereNotIn('user_id', EventGroupContact::where('event_group_id', $exclude_group)->pluck('user_id'));
        }

        $query->orderBy('last_name')->orderBy('first_name');

        $participants = $query->get();


        if ('select2' === $mode) {
            $valueKey = "id";
        }

        if ( ! $participantMapCallback) {
            $participantMapCallback = function ($participant) use ($valueKey, $textKey) {
                $textValue = $participant->last_name.' '.$participant->first_name;
                $htmlValue = $textValue.
                    ($participant?->pec_enabled
                        ? ' <span class="badge rounded-pill bg-'.
                        ($participant->has_paid_grant_deposit
                            ? 'success'
                            : 'warning').'" style="width: 15px; height: 15px">&nbsp;</span>'
                        : '');

                return [
                    $valueKey => $participant->id,
                    $textKey  => $textValue,
                    'html'    => $htmlValue,
                ];
            };
        }

        $col = $participants->map($participantMapCallback);
        if ($pluck) {
            $col = $col->pluck($textKey, $valueKey);
        }

        return $col->toArray();
    }

    public static function getData(int|Event $eventId, int $userId): array
    {
        $instance = new self(event: $eventId, account: $userId);

        $model = $instance->getEventContact();

        if ( ! $model) {
            return [];
        }

        $deposit_paid = $instance->hasPaidGrantDeposit();
        $group        = $model->participationType?->group;

        $data = [
            'event_contact_id'      => $model->id,
            'group'                 => $group,
            'participation_type_id' => $model->participation_type_id,
            'pec_enabled_by_admin'  => $model->pec_enabled,
            'pec_enabled'           => $model->pec_enabled && $deposit_paid,
            'pec_eligible'          => $model->is_pec_eligible,
            'pec_deposit_paid'      => (int)$deposit_paid,
            'pec_authorized'        => $instance->isPecAuthorized(),
            'order_cancellation'    => $model->order_cancellation,
        ];

        $data['group_translated'] = $group ? ParticipantType::translated($group) : null;
        $data['type']             = $group ? Dictionnaries::participationTypes()[$group]->filter(
            fn($item) => $item->id == $model->participation_type_id,
        )->first()?->name : null;
        $data['pec_bookings']     = $instance->getBookedPecServices();

        return $data;
    }

    public function hasTransport(): bool
    {
        return $this->eventContact->transport()->exists();
    }

    public static function getOrdersWithServices(EventContact $ec): EloquentCollection
    {
        return Order::with("services")
            ->where([
                'event_id'    => $ec->event_id,
                'client_type' => OrderClientType::CONTACT->value,
                'client_id'   => $ec->user_id,
            ])
            ->whereHas('services')
            ->get();
    }

    public static function getOrdersWithAccommodations(EventContact $ec): EloquentCollection
    {
        return Order::with("accommodation")
            ->where(['event_id' => $ec->event_id, 'client_id' => $ec->user_id])
            ->whereHas('accommodation')
            ->get();
    }

    public function getAccommodationCarts(): Collection
    {
        $ecOrders = self::getOrdersWithAccommodations($this->eventContact);

        return $ecOrders->load([
            "accommodation.eventHotel.hotel",
            "accommodation.roomGroup",
            "accommodation.room.room",
        ]);
    }

    public function getAccommodationItems(): Collection
    {
        $carts = $this->getAccommodationCarts();

        $ret = [];

        $carts->each(function (Order $orderCart) use (&$ret) {
            $accommodations = $orderCart->accommodation;
            if ($accommodations->isEmpty()) {
                return null;
            }
            foreach ($accommodations as $cartAcc) {
                $dateFormatted        = DateHelper::getFrontDate(Carbon::create($cartAcc->date));
                $hotelName            = $cartAcc->eventHotel->hotel->name;
                $roomGroupName        = strtoupper($cartAcc->roomGroup->name);
                $roomName             = $cartAcc->room->room->name;
                $price                = $cartAcc->total_net + $cartAcc->total_vat;
                $nbPersons            = $cartAcc->quantity;
                $accompanying_details = $cartAcc->accompanying_details;
                $comment              = $cartAcc->comment;
                $processing_fee_ttc   = $cartAcc->processing_fee_ttc / 100;


                $sTitle  = "1 nuit à l'hôtel $hotelName";
                $texts   = [];
                $texts[] = "Le $dateFormatted";
                $texts[] = "Chambre $roomGroupName - $roomName";
                $texts[] = "Prix : $price €";
                $texts[] = "Nombre de personnes : $nbPersons";
                if ($accompanying_details) {
                    $texts[] = "Détails accompagnants : $accompanying_details";
                }
                if ($comment) {
                    $texts[] = "Commentaire : $comment";
                }
                if ($processing_fee_ttc) {
                    $texts[] = "Frais de dossier : $processing_fee_ttc €";
                }


                $ret[] = [
                    'title'       => $sTitle,
                    'text'        => implode('<br>', $texts),
                    'roomgroup'   => $cartAcc->room_group_id,
                    'price'       => $price,
                    'date'        => $cartAcc->date->toDateString(),
                    'has_amended' => $orderCart->amended_order_id,
                    'amend_type'  => $orderCart->amend_type,
                    'was_amended' => $orderCart->amended_by_order_id,
                    'order_id'    => $cartAcc->order_id,

                ];
            }
        });

        return collect($ret);
    }

    public function isOrator(): bool
    {
        return $this->getParticipationTypeGroup() == ParticipantType::ORATOR->value;
    }

    public function isParticipant(): bool
    {
        return $this->getParticipationTypeGroup() == ParticipantType::CONGRESS->value;
    }

    public function getParticipationTypeGroup(): ?string
    {
        return $this->eventContact->participationType?->group;
    }

    public static function getEventGroups(EventContact $ec): EloquentCollection
    {
        return $ec->event
            ->eventGroups()
            ->whereHas("eventGroupContacts", function ($query) use ($ec) {
                $query->where('user_id', $ec->user_id);
            })
            ->get();
    }

    # ORDERS
    public function getOrders(): EloquentCollection
    {
        return Order::query()
            ->where([
                'client_type' => OrderClientType::CONTACT->value,
                'client_id'   => $this->eventContact->user_id,
                'event_id'    => $this->getEvent()->id,
            ])
            ->get();
    }

    /**
     * Ordres pour lesquels le compte est payeur mais pas bénéficiaire
     *
     * @return EloquentCollection
     */
    public function getAssignedOrders(): EloquentCollection
    {
        return Order::query()
            ->select('orders.*')
            ->where(
                fn($where)
                    => $where->where('event_id', $this->getEvent()->id)->where(
                    'client_id',
                    '!=',
                    $this->eventContact->user_id,
                ),
            )
            ->join(
                'order_invoiceable as oi',
                fn($join) => $join->on('orders.id', '=', 'oi.order_id')->where([
                    'oi.account_id'   => $this->eventContact->user_id,
                    'oi.account_type' => OrderClientType::CONTACT->value,
                ]),
            )
            ->get();
    }

    public function getOrdersWithRemainingPayments(): EloquentCollection
    {
        return $this->getOrders()->filter(
            fn($order)
                => $order->marker != OrderMarker::GHOST->value
                &&
                ! $order->external_invoice
                && $order->status == OrderStatus::UNPAID->value,
        )->load('payments');
    }

    /**
     * Paiements à faire sur ordres pour lesquels le compte est payeur mais pas bénéficiaire
     *
     * @return EloquentCollection
     */
    public function getAssignedOrdersWithRemainingPayments(): EloquentCollection
    {
        return $this->getAssignedOrders()->filter(
            fn($order) => ! $order->external_invoice && $order->status == OrderStatus::UNPAID->value,
        )->load('payments');
    }

    public function getAllRemainingPayments(): int|float
    {
        return
            array_sum(OrderAccessor::calculateRemainingAmounts($this->getOrdersWithRemainingPayments())) +
            array_sum(OrderAccessor::calculateRemainingAmounts($this->getAssignedOrdersWithRemainingPayments()));
    }

    public function attributions(): EloquentCollection
    {
        return $this->eventContact->attributions;
    }

    public function serviceAttributions(): EloquentCollection
    {
        return $this->eventContact->serviceAttributions;
    }

    public function accommodationAttributions()//: EloquentCollection
    {
        return $this->eventContact->accommodationAttributions();
    }

    public function grantDeposit(): ?EventDeposit
    {
        $deposit = $this->eventContact->grantDeposit;

        if ( ! $deposit or $deposit->status == EventDepositStatus::TEMP->value) {
            return null;
        }

        return $deposit;
    }

    public function paidGrantDeposit(): ?EventDeposit
    {
        $deposit = $this->grantDeposit();

        if ($deposit and in_array($deposit->status, EventDepositStatus::paid())) {
            return $deposit;
        }

        return null;
    }

    /**
     * @return EloquentCollection
     * TODO: Ce bordel statique a besoin d'un refactoring; hérité de P.Lafitte
     */
    public function getAttributionSummary(): Collection
    {
        $attributedServices = Service::getServiceItems($this->eventContact)
            ->filter(fn($item) => $item['source'] == OrderSource::ATTRIBUTION->value)
            ->map(fn($item)
                => [
                'title'      => $item['title'],
                'text'       => $item['text'],
                'attributed' => $item['badge']['text'] ?? null,
                'type'       => OrderCartType::SERVICE->value,
                'order_id'   => $item['order_id'],
                'event_id'   => $item['event_id'],
                'paid_by'    => $item['paid_by'],
            ]);

        $attributedAccommodation = Accommodation::getAccommodationItems($this->eventContact)
            ->filter(fn($item) => $item['source'] == OrderSource::ATTRIBUTION->value)
            ->map(fn($item)
                => [
                'title'      => $item['title'],
                'text'       => $item['text'],
                'attributed' => $item['badge']['attribution']['text'] ?? null,
                'type'       => OrderCartType::ACCOMMODATION->value,
                'order_id'   => $item['order_id'],
                'event_id'   => $item['event_id'],
                'paid_by'    => $item['paid_by'],
            ]);


        return $attributedServices->merge($attributedAccommodation);
    }

    public function hasOrdersOrGroupOrders(): bool
    {
        if ($this->hasOrdersOrGroupOrders === null) {
            $groupOrder = 0;
            if ($this->eventGroups()->count()) {
                $groupOrder = $this->accommodationAttributions()->count() || $this->serviceAttributions()->count();
            }
            $this->hasOrdersOrGroupOrders = $groupOrder || $this->hasAnyOrders();
        }

        return $this->hasOrdersOrGroupOrders;
    }

    public function eventGroups(): Collection
    {
        if ($this->eventGroups === null) {
            $this->eventGroups = $this->eventContact->eventGroups;
        }

        return $this->eventGroups;
    }
}
