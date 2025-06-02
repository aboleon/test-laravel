<?php

namespace App\Accessors\EventManager;

use App\Accessors\Dictionnaries;
use App\Accessors\EventContactAccessor;
use App\Accessors\EventManager\Availability\AvailabilityRecap;
use App\Accessors\Pec;
use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Models\EventContact;
use App\Models\EventManager\Accommodation as Hotel;
use App\Models\EventManager\Accommodation\BlockedRoom;
use App\Models\EventManager\EventGroup;
use App\Models\Order\Attribution;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\StockTemp;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MetaFramework\Traits\DateManipulator;

class Availability
{

    use DateManipulator;

    private int $booked = 0;
    private int $temp_booked = 0;
    private array $blocked_individual_contingent = [];
    private ?Collection $contingent = null;
    private ?Hotel $hotel = null;
    private ?EventContactAccessor $eventContactAccessor = null;
    private int $event_group_id = 0;
    private ?Collection $blocked_for_groups = null;
    private Collection $blockedIndividualQuery;
    private Collection $tempBookingQuery;
    private Collection $bookingsQuery;

    public int $participation_type = 0;

    private int $room_group_id = 0;
    public ?string $entry_date = null;
    public ?string $out_date = null;
    public ?string $date = null;
    private Builder $query;

    private array $summarizedData = [];
    private Collection $collection;
    /**
     * Ne tenir compte que des chambres publiées
     */
    private bool $published_rooms_only = false;
    private ?Collection $published_rooms = null;
    private ?Collection $room_configs = null;
    private ?Collection $published_roomgroups = null;
    private string $order_uuid = '';
    private array $roomgroups = [];
    private array $bookingsData = [];
    private ?array $recap = null;
    private ?array $event_groups = null;
    private bool $calculated = false;
    private bool $hasRange = false;
    private array $dateRange = [];

    private array $excludedRooms = [];
    private array $cancelledData = [];
    private array $amendedData = [];
    private ?array $roomsIds = null;

    private int|float $minimumPrice = 0;
    private ?bool $accountIsGrantable = null;

    private ?Collection $attributionData = null;

    public function __construct()
    {
        $this->setOrderUuid((string)request('order_uuid'));
    }

    public function calculate(): self
    {
        if ( ! $this->calculated) {
            $this->roomgroups = $this->fetchRoomGroups();

            $this->blockedIndividualQuery = $this->individualBlockedQuery();
            $this->tempBookingQuery       = $this->tempBookingQuery();
            $this->bookingsQuery          = $this->bookingsQuery();
            $this->blocked_for_groups     = $this->getGroupBlockedQuery();

            $this->blocked_individual_contingent = $this->getIndividualBlocked();
            $this->booked                        = $this->getBookingsCount();
            $this->temp_booked                   = $this->getTempBookingsCount();
            $this->fetchPublishedRooms();

            $this->calculated = true;
        }

        return $this;
    }

    // Setters

    public function setParticipationType(int $type): self
    {
        $this->participation_type = $type;

        return $this;
    }

    public function setRoomGroupId(int $id): self
    {
        $this->room_group_id = $id;

        return $this;
    }

    public function setEventGroupId(int $id): self
    {
        $this->event_group_id = $id;

        return $this;
    }

    public function publishedRoomsOnly(): self
    {
        $this->published_rooms_only = true;

        return $this;
    }

    /*
     * Set min price
     * Rooms have to have price greater than it
     */
    public function setMinimumPrice(int|float $minimumPrice): self
    {
        $this->minimumPrice = $minimumPrice;

        return $this;
    }

    public function setDateRange(array $dates): self
    {
        $this->entry_date = $this->parseDate(current($dates));
        $this->out_date   = $this->parseDate(end($dates));

        if ($this->entry_date && $this->out_date && $this->out_date != $this->entry_date) {
            $this->hasRange = true;
            $this->produceDateRange();
        } else {
            // Reset to single date if dates are equal
            $this->entry_date = null;
            $this->out_date   = null;
            $this->date       = $this->parseDate($dates[0]);
        }

        return $this;
    }

    public function setDate(?string $date): self
    {
        $this->date = $this->parseDate($date);

        return $this;
    }


    public function setEventAccommodation(int|Hotel $hotel): self
    {
        $this->hotel = is_int($hotel) ? Hotel::findOrFail($hotel) : $hotel;

        return $this;
    }

    # EventContact specifics
    public function setEventContact(null|int|string|EventContact $contact = null): self
    {
        if ($contact) {
            $this->eventContactAccessor = (new EventContactAccessor())->setEventContact($contact);
        }

        return $this;
    }

    public function getEventContact(): ?EventContactAccessor
    {
        return $this->eventContactAccessor;
    }

    public function accountIsGrantable(): bool
    {
        if (is_null($this->accountIsGrantable)) {
            $this->accountIsGrantable = $this->eventContactAccessor && $this->eventContactAccessor->isPecAuthorized();
        }

        return $this->accountIsGrantable;
    }

    # end EventContact specifics

    private function setOrderUuid(string $uuid = ''): self
    {
        $this->order_uuid = $uuid;

        return $this;
    }

    // Queries

    private function loadContingent(): ?Collection
    {
        if ( ! is_null($this->contingent)) {
            return $this->contingent;
        }

        $this->contingent = $this->hotel->contingent->load('configs.rooms')->sortBy('date');

        return $this->contingent;
    }

    public function getContingent(): array
    {
        $this->collection = $this->loadContingent();


        $this->filterByDate();
        $this->filterByRoomGroup();

        return $this->collection
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(fn($date) => $date->mapWithKeys(fn($c) => [$c->room_group_id => $c->total]))
            ->toArray();
    }

    private function parseRoomConfigs(): Collection
    {
        if ($this->room_configs !== null) {
            return $this->room_configs;
        }

        $this->room_configs = $this
            ->loadContingent()
            ->reduce(fn($items, $item)
                => $items->put(
                $item->getRawOriginal('date'),
                $items
                    ->get($item->getRawOriginal('date'), collect())
                    ->put(
                        $item->room_group_id,
                        $item->configs
                            ->filter(fn($config) => ! is_null($config->rooms))
                            ->map(fn($config)
                                => [
                                'id'             => $config->room_id,
                                'price'          => $config->sell,
                                'capacity'       => $config->rooms->capacity ?? 0,
                                'published'      => $config->published,
                                'pec'            => $config->pec,
                                'pec_allocation' => $config->pec_allocation,
                                'service_id'     => $config->service_id,
                            ]),
                    ),
            ),
                collect(),
            );


        return $this->room_configs;
    }

    /**
     * With filters
     *
     * @return array
     */
    public function fetchRoomConfigs(): array
    {
        $this->collection = $this->parseRoomConfigs();

        if ($this->excludedRooms) {
            $this->collection = $this->filterExcludedRooms($this->collection);
        }

        if ($this->minimumPrice > 0) {
            $this->collection = $this->filterMinimumPrice($this->collection);
        }

        $this->filterByDateAsKey();

        if ($this->room_group_id) {
            $this->collection = $this->collection->map(
                fn($rooms)
                    => $rooms->has($this->room_group_id) ? collect(
                    [$this->room_group_id => $rooms->get($this->room_group_id)],
                ) : null,
            )->filter();
        }

        if ($this->published_rooms_only && ! is_null($this->published_rooms)) {
            if ($this->date) {
                $this->published_rooms = $this->published_rooms->filter(fn($item, $key) => $key == $this->date);
            }

            $this->collection = $this->published_rooms->mapWithKeys(
                fn($items, $date)
                    => [
                    $date => collect($items)->mapWithKeys(
                        fn($ids, $key)
                            => [
                            $key => isset($this->collection[$date][$key])
                                ? collect($this->collection[$date][$key])->filter(
                                    fn($item) => $ids->contains($item['id']),
                                )->values()
                                : collect(),
                        ],
                    ),
                ],
            );
        }

        return $this->collection->toArray();
    }

    private function filterMinimumPrice(Collection $collection): Collection
    {
        if ($this->minimumPrice < 1) {
            return $collection;
        }

        return $collection->map(function ($dateCollection) {
            return $dateCollection->map(function ($timeCollection) {
                return $timeCollection->filter(function ($item) {
                    return $item['price'] > $this->minimumPrice;
                })->values(); // Re-index the collection after filtering
            });
        });
    }

    private function filterExcludedRooms(Collection $collection)
    {
        if ( ! $this->excludedRooms) {
            return $collection;
        }

        return $collection->map(function ($roomGroups) {
            return collect($roomGroups)->map(function ($rooms) {
                return collect($rooms)->reject(function ($room) {
                    return in_array($room['id'], $this->excludedRooms);
                })->values();
            })->reject(function ($rooms) {
                return $rooms->isEmpty();
            });
        })->reject(function ($roomGroups) {
            return $roomGroups->isEmpty();
        });
    }


    private function fetchRoomGroups(): array
    {
        $this->collection = $this->hotel->roomGroups->load('rooms')->mapWithKeys(fn($item)
            => [
            $item->id => [
                'name'  => $item->name,
                'rooms' => $item->rooms->mapWithKeys(
                    fn($room) => [$room->id => Dictionnaries::entry('type_chambres', $room->room_id)->name],
                )->toArray(),
            ],
        ]);

        // Filter by specific room group ID if provided
        if ($this->room_group_id) {
            $this->collection = $this->collection->only($this->room_group_id);
        }

        // Filter by published rooms only if specified
        if ($this->published_rooms_only && ! is_null($this->published_rooms)) {
            $this->collection = $this->collection->map(fn($item, $key)
                => [
                'name'  => $item['name'],
                'rooms' => collect($item['rooms'])->filter(
                    fn($room, $roomId)
                        => $this->published_rooms
                        ->flatMap(fn($dates) => $dates)
                        ->flatMap(fn($ids) => $ids)
                        ->contains($roomId),
                )->toArray(),
            ])->reject(fn($value) => empty($value['rooms'])); // Reject if rooms are empty
        }

        // Final rejection of any room groups with empty rooms
        return $this->collection->reject(fn($item) => empty($item['rooms']))->toArray();
    }


    private function individualBlockedQuery(): Collection
    {
        $this->query = BlockedRoom::query()->where('event_accommodation_id', $this->hotel->id);

        $this->queryByDate();
        $this->queryByRoomGroup();

        return $this->query->get();
    }

    public function bookingsQuery(): Collection
    {
        $this->query = AccommodationCart::query()->where('event_hotel_id', $this->hotel->id)
            ->select(
                'order_id',
                'on_quota',
                'date',
                'order_cart_accommodation.cancelled_qty',
                'order_cart_accommodation.total_pec',
                'room_group_id',
                'order_cart_accommodation.quantity',
                DB::raw('CAST(COALESCE(c.participation_type_id, 0) AS SIGNED) AS participation_type_id'),
                'c.client_id as account_id',
                'c.client_type as account_type',
                'c.amend_type',
                'c.amended_by_order_id as was_amended',
                'c.amended_order_id as has_amended',
                'amended_cart_id',

                'c.cancelled_at as order_cancelled_at',
                DB::raw('CASE WHEN c.amended_order_id IS NOT NULL THEN c.client_type ELSE NULL END as has_amended_client_type'),
            )
            ->join('orders as c', fn($join) => $join->on('c.id', '=', 'order_cart_accommodation.order_id'));

        $this->queryByDate();
        $this->queryByRoomGroup();

        return $this->query->get();
    }

    private function tempBookingQuery(): Collection
    {
        $this->query = StockTemp::query()->whereIn('shoppable_id', array_keys($this->roomgroups));

        $this->queryByDate();
        $this->queryByRoomGroup('shoppable_id');

        return $this->query->get();
    }

    private function queryByDate(): void
    {
        if ($this->date) {
            $this->query->where('date', $this->date);
        } else {
            if ($this->entry_date && ! $this->out_date) {
                $this->query->where('date', '>=', $this->entry_date);
            } elseif ( ! $this->entry_date && $this->out_date) {
                $this->query->where('date', '<', $this->out_date);
            } elseif ($this->entry_date && $this->out_date) {
                $this->query->whereRaw('date >= ? and date < ?', [$this->entry_date, $this->out_date]);
            }
        }
    }

    private function queryByRoomGroup(string $filterable = 'room_group_id'): void
    {
        if ($this->room_group_id) {
            $this->query->where($filterable, $this->room_group_id);
        }
    }

    private function filterByDate(): void
    {
        if ($this->date) {
            $this->collection = $this->collection->filter(fn($item) => $item->getRawOriginal('date') == $this->date);
        } else {
            if ($this->entry_date && ! $this->out_date) {
                $this->collection = $this->collection->filter(
                    fn($item) => $item->getRawOriginal('date') >= $this->entry_date,
                );
            } elseif ( ! $this->entry_date && $this->out_date) {
                $this->collection = $this->collection->filter(
                    fn($item) => $item->getRawOriginal('date') < $this->out_date,
                );
            } elseif ($this->entry_date && $this->out_date) {
                $this->collection = $this->collection->filter(
                    fn($item)
                        => $item->getRawOriginal('date') >= $this->entry_date
                        && $item->getRawOriginal('date') < $this->out_date,
                );
            }
        }
    }

    private function filterByDateAsKey(): void
    {
        if ($this->date) {
            $this->collection = $this->collection->filter(fn($item, $date) => $this->date == $date);
        } else {
            if ($this->entry_date && ! $this->out_date) {
                $this->collection = $this->collection->filter(fn($item, $date) => $date >= $this->entry_date);
            } elseif ( ! $this->entry_date && $this->out_date) {
                $this->collection = $this->collection->filter(fn($item, $date) => $date < $this->out_date);
            } elseif ($this->entry_date && $this->out_date) {
                $this->collection = $this->collection->filter(
                    fn($item, $date) => $date >= $this->entry_date && $date < $this->out_date,
                );
            }
        }
    }

    private function filterByRoomGroup(): void
    {
        if ($this->room_group_id) {
            $this->collection = $this->collection->filter(fn($item) => $item->room_group_id == $this->room_group_id);
        }
    }


    public function getGroupBlockedQuery(): Collection
    {
        if ($this->blocked_for_groups !== null) {
            return $this->blocked_for_groups;
        }

        $this->collection = $this->hotel->event->eventGroups
            ->load('blockedRooms')
            ->pluck('blockedRooms')
            ->flatten()
            ->filter(fn($item) => $item->event_accommodation_id == $this->hotel->id);

        $this->filterByDate();
        $this->filterByRoomGroup();

        return $this->collection;
    }

    public function getGroupAttributions(): array
    {
        $this->attributionsQuery();

        return $this->attributionData->groupBy(function ($item) {
            return $item->configs['date'] ?? null;
        })->map(function ($group) {
            return $group->sum('quantity');
        })->toArray();
    }

    // Getters

    public function hasParticipationType(): bool
    {
        return $this->getParticipationType() != 0;
    }

    public function isGroup(): bool
    {
        return $this->getEventGroupId() != 0;
    }

    public function getParticipationType(): int
    {
        return $this->participation_type;
    }

    public function getEventGroupId(): int
    {
        return $this->event_group_id;
    }

    public function baseGroupId(?int $group_id = null): int
    {
        return array_flip($this->getEventGroups())[$group_id ?? $this->event_group_id] ?? 0;
    }

    public function eventGroupIdFromBaseId(?int $group_id = null): int
    {
        return $this->getEventGroups()[$group_id ?? $this->event_group_id] ?? 0;
    }

    private function fetchPublishedRooms(): Collection
    {
        if ($this->published_rooms !== null) {
            return $this->published_rooms;
        }

        $this->parseRoomConfigs();

        $this->collection = $this->room_configs;

        if ($this->excludedRooms) {
            $this->collection = $this->filterExcludedRooms($this->collection);
        }

        $this->published_rooms = $this->collection->map(function ($roomGroups) {
            return collect($roomGroups)->map(function ($rooms) {
                return collect($rooms)->filter(function ($room) {
                    return $room['published'] == 1;
                })->pluck('id')->values();
            })->reject(function ($rooms) {
                return $rooms->isEmpty();
            });
        })->reject(function ($roomGroups) {
            return $roomGroups->isEmpty();
        });


        if ($this->room_group_id) {
            $this->published_rooms = $this->published_rooms->map(fn($item) => $item->only($this->room_group_id));
        }

        $this->published_roomgroups = $this->published_rooms->mapWithKeys(fn($item, $date)
            => [
            $date => $item->keys(),
        ]);


        return $this->published_rooms;
    }

    private function getTotalBlockedCount(): int
    {
        return $this->blockedIndividualQuery->sum('total') + $this->getGroupBlockedQuery()->sum('total');
    }

    public function getBookingsCount(): int
    {
        return $this->bookingsQuery->sum('quantity');
    }

    public function getFilteredBookings(string $account_type = 'contact'): array
    {
        return $this->bookingsQuery
            ->filter(fn($item)
                => ($account_type != OrderClientType::GROUP->value
                ? $item->account_type != OrderClientType::GROUP->value
                : $item->account_type == $account_type),
            )
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('room_group_id')->mapWithKeys(function ($roomGroup, $roomGroupId) {
                    $onQuotaSum = $roomGroup->filter(fn($item) => $item->on_quota == 1)->sum('quantity');
                    $freeSum    = $roomGroup->filter(fn($item) => $item->on_quota == 0)->sum('quantity');

                    return [
                        $roomGroupId => [
                            'on_quota' => $onQuotaSum,
                            'free'     => $freeSum,
                            'total'    => $onQuotaSum + $freeSum,
                        ],
                    ];
                });
            })->toArray();
    }

    public function getFreeBookings(): array
    {
        return $this->bookingsQuery
            ->filter(fn($item) => $item->account_type == OrderClientType::CONTACT->value && $item->participation_type_id == 0)
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('room_group_id')->map->sum('quantity');
            })->toArray();
    }


    public function getGroupBookings(array $filter = ['account_id', 'date']): array
    {
        return $this->bookingsQuery
            ->filter(fn($item) => $item->account_type === OrderClientType::GROUP->value)
            ->groupBy(function ($filterGroup) use ($filter) {
                $column = current($filter);

                return $column === 'date' ? $filterGroup->getRawOriginal('date') : $filterGroup->{$column};
            })
            ->mapWithKeys(function ($items, $key) use ($filter) {
                $isFirstFilterDate = current($filter) === 'date';

                return [
                    $isFirstFilterDate ? $key : $this->eventGroupIdFromBaseId((int)$key) => $items
                        ->groupBy(function ($dateItem) use ($filter) {
                            $column = end($filter);

                            return $column === 'date' ? $dateItem->getRawOriginal('date') : $dateItem->{$column};
                        })
                        ->mapWithKeys(function ($dateItems, $innerKey) use ($filter) {
                            $isSecondFilterAccountId = end($filter) === 'account_id';

                            return [
                                $isSecondFilterAccountId
                                    ? $this->eventGroupIdFromBaseId((int)$innerKey)
                                    : $innerKey => $dateItems
                                    ->groupBy('room_group_id')
                                    ->mapWithKeys(function ($groupItems, $roomGroupId) {
                                        $onQuotaSum = $groupItems->filter(fn($item) => $item->on_quota == 1)->sum('quantity');
                                        $freeSum    = $groupItems->filter(fn($item) => $item->on_quota == 0)->sum('quantity');

                                        return [
                                            $roomGroupId => [
                                                'on_quota' => $onQuotaSum,
                                                'free'     => $freeSum,
                                            ],
                                        ];
                                    }),
                            ];
                        }),
                ];
            })->toArray();
    }


    public function getEventGroups(): array
    {
        if ($this->event_groups !== null) {
            return $this->event_groups;
        }

        return $this->event_groups = EventGroup::query()->where('event_id', $this->hotel->event->id)->pluck(
            'id',
            'group_id',
        )->toArray();
    }

    public function getCurrentOrderTempBookings(): array
    {
        if ( ! $this->order_uuid) {
            return [];
        }

        return $this->tempBookingQuery
            ->filter(fn($item) => $item->uuid == $this->order_uuid)
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shoppable_id')->mapWithKeys(function ($roomGroup, $roomGroupId) {
                    return [
                        $roomGroupId => $roomGroup->groupBy('participation_type_id')->map->sum('quantity'),
                    ];
                });
            })->toArray();
    }

    public function getTempBookingsCount(?string $account_type = null): int
    {
        $data = $this->tempBookingQuery;

        if ($account_type) {
            $data = $data->filter(fn($item) => $account_type == OrderClientType::GROUP->value ? $item->account_type == $account_type : $item->account_type != OrderClientType::GROUP->value);
        }

        return $data->sum('quantity');
    }

    public function getTempBookings(): array
    {
        return $this->tempBookingQuery
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shoppable_id')->map(fn($shoppableGroup) => $shoppableGroup->sum('quantity'),
                );
            })
            ->toArray();
    }

    public function getTotalBookings(): int
    {
        return $this->booked + $this->temp_booked;
    }

    private function getBookingsByParticipationType(bool $pec = false): array
    {
        $query = $this->bookingsQuery;
        if ($pec) {
            $query = $this->bookingsQuery->filter(fn($item) => $item->total_pec > 0);
        }

        return $query
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('room_group_id')->mapWithKeys(function ($roomGroup, $roomGroupId) {
                    return [
                        $roomGroupId => $roomGroup->groupBy('participation_type_id')->map->sum('quantity'),
                    ];
                });
            })->toArray();
    }

    private function getTempBookingsByParticipationType(): array
    {
        return $this->tempBookingQuery
            ->filter(fn($item) => $item->account_type == 'contact' && $item->participation_type_id != 0)
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shoppable_id')->mapWithKeys(function ($roomGroup, $roomGroupId) {
                    return [
                        $roomGroupId => $roomGroup->groupBy('participation_type_id')->map->sum('quantity'),
                    ];
                });
            })->toArray();
    }

    private function getTempBookingsPec(): array
    {
        return $this->tempBookingQuery
            ->filter(fn($item) => $item->account_type == 'contact' && $item->pec != 0)
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shoppable_id')->mapWithKeys(function ($roomGroup, $roomGroupId) {
                    return [
                        $roomGroupId => $roomGroup->groupBy('participation_type_id')->map->sum('quantity'),
                    ];
                });
            })->toArray();
    }

    private function getTempBookingsForGroups(): array
    {
        return $this->tempBookingQuery
            ->filter(fn($item) => $item->account_type == 'group')
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shoppable_id')->mapWithKeys(function ($roomGroup, $roomGroupId) {
                    return [
                        $roomGroupId => $roomGroup->groupBy('account_id')->mapWithKeys(function ($quantityGroup, $accountId) {
                            $onQuotaSum = $quantityGroup->filter(fn($item) => $item->on_quota == 1)->sum('quantity');
                            $freeSum    = $quantityGroup->filter(fn($item) => $item->on_quota == 0)->sum('quantity');

                            return [
                                $this->eventGroupIdFromBaseId($accountId) => [
                                    'on_quota'       => $onQuotaSum,
                                    'free'           => $freeSum,
                                    'base_group_id'  => $accountId,
                                    'event_group_id' => $this->eventGroupIdFromBaseId($accountId),
                                ],
                            ];
                        }),
                    ];
                });
            })->toArray();
    }


    /**
     * @return array
     * [event_group_id] => [
     *       date => [
     *            room_group_id => sum(total)
     *       ]
     * ]
     */
    public function getGroupBlockedByEventGroup(): array
    {
        return $this->blocked_for_groups
            ->groupBy('event_group_id')
            ->map(function ($groupItems) {
                return $groupItems->groupBy(fn($item) => $item->getRawOriginal('date'))->map(function ($dateItems) {
                    return $dateItems->groupBy('room_group_id')->map(function ($roomGroupItems) {
                        return $roomGroupItems->sum('total');
                    });
                });
            })->toArray();
    }

    /**
     * @return array
     * [date] => [
     *       room_group _id => [
     *            event_group_id => total
     *       ]
     * ]
     */
    public function getGroupBlockedByDateAndRoomGroup(): array
    {
        return $this->blocked_for_groups->groupBy(fn($item) => $item->getRawOriginal('date'))->map(
            function ($dateGroup) {
                return $dateGroup->groupBy('room_group_id')->map(function ($roomGroup) {
                    return $roomGroup->mapWithKeys(function ($item) {
                        return [$item['event_group_id'] => $item['total']];
                    });
                });
            },
        )->toArray();
    }

    public function getIndividualBlocked(): array
    {
        return $this->blockedIndividualQuery
            ->groupBy([fn($item) => $item->getRawOriginal('date'), 'room_group_id'])
            ->map(function ($date) {
                return $date->mapWithKeys(function ($items, $roomGroupId) {
                    return [
                        $roomGroupId => [
                            'total'              => $items->sum('total'),
                            'grant'              => $items->sum('grant'),
                            'participation_type' => explode(
                                ',',
                                $items->pluck('participation_type')->unique()->join(','),
                            ),
                        ],
                    ];
                });
            })
            ->toArray();
    }

    private function getIndividualBlockedByParticipationType(): array
    {
        return
            $this->blockedIndividualQuery
                ->flatMap(function ($item) {
                    return collect(explode(',', $item->participation_type))
                        ->map(function ($type) use ($item) {
                            return [
                                'participation_type' => $type,
                                'date'               => $item->getRawOriginal('date'),
                                'room_group_id'      => $item->room_group_id,
                                'total'              => $item->total,
                            ];
                        });
                })
                ->groupBy(['participation_type', 'date', 'room_group_id'])
                ->map(function ($types) {
                    return $types->map(function ($dates) {
                        return $dates->mapWithKeys(function ($items, $roomGroupId) {
                            return [$roomGroupId => $items->sum('total')];
                        });
                    });
                })
                ->toArray();
    }


    // Final Data Getters

    public function get(string $key, $default = null): mixed
    {
        // return $this->getSummarizedData()[$key] ?? null;
        return Arr::get($this->getSummarizedData(), $key, $default);
    }

    public function getSummarizedData(): array
    {
        if ($this->summarizedData) {
            return $this->summarizedData;
        }

        $this->generateData();

        return $this->summarizedData;
    }

    public function generateData(): self
    {
        $this->calculate();
        $this->summarizedData = [
            'event_accommodation_id' => $this->hotel->id,
            'event_id'               => $this->hotel->event->id,
            'event_group_id'         => $this->event_group_id,
            'room_group_id'          => $this->room_group_id,
            'dates'                  => [
                'entry_date' => $this->entry_date,
                'out_date'   => $this->out_date,
                'date'       => $this->date,
                'date_range' => $this->getEffectiveDateRange(),
                'formatted'  => [
                    'date'       => $this->date ? $this->toDateFormat('d/m/Y', $this->date) : null,
                    'entry_date' => $this->entry_date ? $this->toDateFormat('d/m/Y', $this->entry_date) : null,
                    'out_date'   => $this->out_date ? $this->toDateFormat('d/m/Y', $this->out_date) : null,
                ],
            ],
            'minimum_price'          => $this->minimumPrice,
            'participation_type'     => $this->participation_type,
            'event_groups'           => $this->getEventGroups(),
            'contingent'             => $this->getContingent(),
            'room_configs'           => $this->fetchRoomConfigs(),
            'room_ids'               => $this->getRoomIds(),
            'roomgroups'             => $this->roomgroups,
            'excluded_rooms'         => $this->getExcludedRooms(),
            'published_rooms'        => $this->published_rooms?->toArray(),
            'published_roomgroups'   => $this->published_roomgroups?->toArray(),
            'booked'                 => [
                'total_confirmed'                     => $this->booked,
                'total_temp'                          => $this->getTempBookingsCount(),
                'total'                               => $this->getTotalBookings(),
                'confirmed_individual'                => $this->getFilteredBookings(),
                'confirmed_individual_free'           => $this->getFreeBookings(),
                'confirmed_groups'                    => $this->getFilteredBookings('group'),
                'confirmed_groups_by_group'           => $this->getGroupBookings(),
                'confirmed_groups_by_date'            => $this->getGroupBookings(['date', 'account_id']),
                'confirmed_by_participation_type'     => $this->getBookingsByParticipationType(),
                'confirmed_by_participation_type_pec' => $this->getBookingsByParticipationType(pec: true),
                'temp'                                => $this->getTempBookings(),
                'temp_by_participation_type'          => $this->getTempBookingsByParticipationType(),
                'temp_pec'                            => $this->getTempBookingsPec(),
                'temp_for_groups'                     => $this->getTempBookingsForGroups(),
                'temp_current_order'                  => $this->getCurrentOrderTempBookings(),
                'cancelled'                           => $this->generateCancelledQuota(),
                'amended'                             => $this->amendedData,
                'grants'                              => $this->getGrantDistributed(),
            ],
            'blocked'                => [
                'total'                            => $this->getTotalBlockedCount(),
                'total_individual'                 => $this->blockedIndividualQuery->sum('total'),
                'total_groups'                     => $this->getGroupBlockedQuery()->sum('total'),
                'individual'                       => $this->getIndividualBlocked(),
                'individual_by_participation_type' => $this->getIndividualBlockedByParticipationType(),
                'groups_by_event_group_id'         => $this->getGroupBlockedByEventGroup(),
                'groups_by_date_and_room_group'    => $this->getGroupBlockedByDateAndRoomGroup(),
                'grants'                           => $this->getBlockedForGrants(),
            ],
            'attributions'           => [
                'total_groups' => $this->getGroupAttributions(),
                // 'blocked_groups' => $this->getGroupAttributionsFromBlocked()
            ],
        ];

        $this->summarizedData['blocked']['participation_types'] = array_keys(
            $this->summarizedData['blocked']['individual_by_participation_type'],
        );
        $this->summarizedData['booked']['confirmed_by_blocked_participation_type']
                                                                = $this->filterBookedPyBlockedParticipationTypes(
            $this->summarizedData['booked']['confirmed_by_participation_type'],
            $this->summarizedData['blocked']['participation_types'],
        );


        return $this;
    }

    public function getRoomIds(): array
    {
        if ($this->roomsIds === null) {
            $this->roomsIds = [];
            foreach ($this->roomgroups as $groupData) {
                if (isset($groupData['rooms']) && is_array($groupData['rooms'])) {
                    $this->roomsIds = array_merge($this->roomsIds, array_keys($groupData['rooms']));
                }
            }
        }

        return $this->roomsIds;
    }

    public function getRoomConfigs(): array
    {
        return $this->getSummarizedData()['room_configs'] ?? [];
    }

    public function getRoomGroups(): array
    {
        return $this->getSummarizedData()['roomgroups'] ?? [];
    }

    private function getPublishedRooms(): array
    {
        return $this->getSummarizedData()['published_rooms'] ?? [];
    }

    public function getEffectiveDateRange(): array
    {
        return $this->dateRange;
    }

    public function getStrictAvaiability(): array
    {
        $availabilityCollection = collect($this->getAvailability());

        $commonKeys = $availabilityCollection
            ->map(function ($roomGroups) {
                return collect($roomGroups)->keys();
            })
            ->reduce(function ($carry, $keys) {
                return $carry === null ? $keys : $carry->intersect($keys);
            });

        $filteredAvailability = $availabilityCollection
            ->map(function ($roomGroups) use ($commonKeys) {
                return collect($roomGroups)
                    ->only($commonKeys)
                    ->toArray();
            })
            ->filter(function ($roomGroups) {
                return ! empty($roomGroups);
            })->toArray();

        if ( ! $filteredAvailability) {
            return [];
        }

        $allDatesPresent = count(array_intersect($this->getEffectiveDateRange(), array_keys($this->getAvailability())))
            === count($this->getEffectiveDateRange());

        return $allDatesPresent ? $this->getAvailability() : [];
    }

    public function getAvailability(): array
    {
        $data = $this->getSummarizedData();

        if ( ! is_null($this->recap)) {
            return $this->recap;
        }

        if ( ! $data['contingent']) {
            return [];
        }

        $availability       = [];
        $availability_recap = (new AvailabilityRecap($this));

        foreach ($data['contingent'] as $date => $contingent) {
            $subdata = [];
            foreach ($contingent as $roomgroup => $stock) {
                $recap = $availability_recap->get($date, $roomgroup);


                /* Control example dump
                if ($date == '2025-03-03' && $roomgroup == 71) {
                    //d($this->accountIsGrantable(), 'accountIsGrantable');
                    d($recap, 'bookings_data for '.$date.' - '.$roomgroup);
                }
*/

                // Check if the room meets the minimum price criterion before calculating availability
                if ($this->minimumPrice > 0 && isset($data['room_configs'][$date][$roomgroup])) {
                    $rooms = collect($data['room_configs'][$date][$roomgroup])->filter(function ($room) {
                        return $room['price'] >= $this->minimumPrice;
                    });

                    if ($rooms->isEmpty()) {
                        continue; // Skip this roomgroup if no rooms meet the minimum price
                    }
                }

                if ($this->published_rooms_only) {
                    if ( ! $this->published_roomgroups->has($date) or ! $this->published_roomgroups[$date]->contains(
                            $roomgroup,
                        )
                    ) {
                        continue;
                    }
                }

                $formula
                    = // BASE SUMMARY
                    $stock
                    // Substract all blocked
                    - $recap['blocked']['total']
                    // Substract all bookings
                    - ($recap['confirmed']['total'] + $recap['temp']['total'])
                    // Add quota individual bookings
                    + ($recap['confirmed']['total_individual_quota'] + $recap['temp']['total_individual_quota'])
                    // Add quota group bookings
                    + ($recap['confirmed']['total_groups_quota'] + $recap['temp']['total_groups_quota'])
                    // Add quota pec bookings
                    + ($recap['pec']['to_add']);

                if ($this->hasParticipationType()) {
                    $blocked_for_ptype                       = $recap['blocked']['by_ptype'][$this->getParticipationType()] ?? 0;
                    $booked_by_blocked_for_particiation_type = ($recap['confirmed']['by_blocked_ptype'][$this->getParticipationType()] ?? 0) + ($recap['temp']['by_blocked_ptype'][$this->getParticipationType()] ?? 0);

                    if ($blocked_for_ptype) {
                        $formula += ($blocked_for_ptype - $booked_by_blocked_for_particiation_type);
                    }
                }

                $formula += $recap['pec']['to_substract_from_blocked_ptype'];

                if ($this->isGroup()) {
                    if (
                        $recap['this_group']['blocked'] > 0
                        && $recap['this_group']['remaining'] > 0
                    ) {
                        $formula = $recap['this_group']['remaining'];
                    }
                } else {
                    // Substract booked for groups that have blocked quotas
                    $formula += $recap['confirmed']['total_groups_quota'] ?? 0;
                    $formula += $recap['temp']['total_groups_quota'] ?? 0;
                }

                $subdata[$roomgroup] = $formula;
            }

            $availability[$date] = $subdata;
        }

        $this->recap
            = array_filter($availability, fn($item) => ! empty($item));


        return $this->recap;
    }

    public function getRoomGroupAvailability(
        ?string $date = null,
        ?int $roomgroup = null,
    ): int {
        return $this->getAvailability()[$date ?: $this->date][$roomgroup ?: $this->room_group_id] ?? 0;
    }

    public function getRoomGroup(
        ?int $roomgroup = null,
    ): array {
        return $this->getRoomGroups()[$roomgroup ?: $this->room_group_id] ?? [];
    }

    public function getExcludedRooms(): array
    {
        return $this->excludedRooms;
    }

    public function setExcludeRoomsId(
        array|int $id,
    ): self {
        if ( ! $id) {
            return $this;
        }

        if (is_array($id) && $id) {
            $this->excludedRooms = array_merge($this->excludedRooms, $id);
        } else {
            $this->excludedRooms[] = $id;
        }

        return $this;
    }

    private function filterBookedPyBlockedParticipationTypes(
        array $confirmed_by_participation_type,
        array $participation_types,
    ): array {
        if ( ! $confirmed_by_participation_type or ! $participation_types) {
            return [];
        }

        return collect($confirmed_by_participation_type)
            ->map(function ($date) use ($participation_types) {
                return collect($date)
                    ->map(function ($types) use ($participation_types) {
                        return Arr::only($types, $participation_types);
                    })
                    ->filter(function ($types) {
                        return ! empty($types);
                    });
            })
            ->filter(function ($date) {
                return ! $date->isEmpty();
            })->toArray();
    }

    private function produceDateRange(): void
    {
        $period = CarbonPeriod::create($this->entry_date, '1 day', Carbon::parse($this->out_date)->subDay());

        foreach ($period as $date) {
            $this->dateRange[] = $date->format('Y-m-d'); // Format each date as 'Y-m-d'
        }
    }

    public function generateCancelledQuota(
        ?string $date = null,
        ?int $roomgroup = null,
    ): array {
        if ( ! $date && ! $roomgroup && $this->cancelledData) {
            return $this->cancelledData;
        }

        $this->cancelledData = [
            'total'    => 0,
            'by_ptype' => [],
            'on_quota' => 0, // Add on_quota
            'free'     => 0, // Add free
        ];

        $cancelledOrders = $this->bookingsQuery
            ->filter(fn($item) => ($item->order_cancelled_at !== null || $item->cancelled_qty > 0));

        if ($date) {
            $cancelledOrders = $cancelledOrders->filter(
                fn($item) => $item->getRawOriginal('date') === $date,
            );
        }

        if ($roomgroup) {
            $cancelledOrders = $cancelledOrders->filter(
                fn($item) => $item->room_group_id === $roomgroup,
            );
        }

        // Calculate on_quota and free
        $onQuotaSum = $cancelledOrders->filter(fn($item) => $item->on_quota == 1)->sum('cancelled_qty');
        $freeSum    = $cancelledOrders->filter(fn($item) => $item->on_quota == 0)->sum('cancelled_qty');

        $this->cancelledData['total']    = $cancelledOrders->sum('cancelled_qty');
        $this->cancelledData['on_quota'] = $onQuotaSum;
        $this->cancelledData['free']     = $freeSum;

        $this->cancelledData['by_ptype'] = $cancelledOrders
            ->groupBy('participation_type_id')
            ->map(fn($group)
                => [
                'total'    => $group->sum('cancelled_qty'),
                'on_quota' => $group->filter(fn($item) => $item->on_quota == 1)->sum('cancelled_qty'),
                'free'     => $group->filter(fn($item) => $item->on_quota == 0)->sum('cancelled_qty'),
            ])
            ->toArray();

        // Add date/room grouping
        $this->cancelledData['by_date'] = $cancelledOrders
            ->groupBy(fn($order) => $order->getRawOriginal('date'))
            ->map(fn($dateGroup)
                => $dateGroup
                ->groupBy('room_group_id')
                ->map(fn($roomGroup)
                    => [
                    'total'    => $roomGroup->sum('cancelled_qty'),
                    'on_quota' => $roomGroup->filter(fn($item) => $item->on_quota == 1)->sum('cancelled_qty'),
                    'free'     => $roomGroup->filter(fn($item) => $item->on_quota == 0)->sum('cancelled_qty'),
                ])
                ->toArray(),
            )
            ->toArray();

        $this->cancelledData['orders'] = $cancelledOrders->toArray();

        return $this->cancelledData;
    }


    public function generateAmendedQuota(
        ?string $date = null,
        ?int $roomgroup = null,
    ): array {
        if ( ! $date && ! $roomgroup && $this->amendedData) {
            return $this->amendedData;
        }

        $this->amendedData = [
            'total'    => 0,
            'by_ptype' => [],
            'on_quota' => 0, // Add on_quota
            'free'     => 0, // Add free
        ];

        // Filter amended orders
        $amendedOrders = $this->bookingsQuery
            ->filter(function ($item) {
                // Check if the order was amended (either fully or partially)
                return ($item->was_amended !== null or $item->has_amended !== null)
                    && ($item->order_cancelled_at === null && $item->cancelled_qty == 0); // Ensure not cancelled
            });

        if ($date) {
            $amendedOrders = $amendedOrders->filter(
                fn($item) => $item->getRawOriginal('date') === $date,
            );
        }

        if ($roomgroup) {
            $amendedOrders = $amendedOrders->filter(
                fn($item) => $item->room_group_id === $roomgroup,
            );
        }

        // Filter for partial amendments (amend_type = cart)
        $amendedOrders = $amendedOrders->filter(function ($item) {
            // If the amendment is partial (amend_type = cart), ensure the amended_cart_id matches
            if ($item->amend_type === 'cart') {
                return $item->amended_cart_id !== null;
            }

            return true; // Include full amendments
        });

        // Calculate on_quota and free
        $onQuotaSum = $amendedOrders->filter(fn($item) => $item->on_quota == 1)->sum('quantity');
        $freeSum    = $amendedOrders->filter(fn($item) => $item->on_quota == 0)->sum('quantity');

        $this->amendedData['total']    = $amendedOrders->sum('quantity');
        $this->amendedData['on_quota'] = $onQuotaSum;
        $this->amendedData['free']     = $freeSum;

        $this->amendedData['by_ptype'] = $amendedOrders
            ->groupBy('participation_type_id')
            ->map(fn($group)
                => [
                'total'    => $group->sum('quantity'),
                'on_quota' => $group->filter(fn($item) => $item->on_quota == 1)->sum('quantity'),
                'free'     => $group->filter(fn($item) => $item->on_quota == 0)->sum('quantity'),
            ])
            ->toArray();

        // Add date/room grouping
        $this->amendedData['by_date'] = $amendedOrders
            ->groupBy(fn($order) => $order->getRawOriginal('date'))
            ->map(fn($dateGroup)
                => $dateGroup
                ->groupBy('room_group_id')
                ->map(fn($roomGroup)
                    => [
                    'total'    => $roomGroup->sum('quantity'),
                    'on_quota' => $roomGroup->filter(fn($item) => $item->on_quota == 1)->sum('quantity'),
                    'free'     => $roomGroup->filter(fn($item) => $item->on_quota == 0)->sum('quantity'),
                ])
                ->toArray(),
            )
            ->toArray();

        $this->amendedData['orders'] = $amendedOrders->toArray();

        return $this->amendedData;
    }

    public function getBlockedForGrants(): array
    {
        return $this->hotel->grant
            ->groupBy(fn($item) => $item->getRawOriginal('date'))
            ->map(fn($items) => $items->mapWithKeys(fn($item)
                => [
                $item->room_group_id => $item->total,
            ]))
            ->toArray();
    }

    public function getGrantDistributedDetail(): array
    {
        return Pec::getPecDistributedForHotelId($this->hotel->id);
    }

    public function getGrantDistributed(): array
    {
        return collect($this->getGrantDistributedDetail())
            ->groupBy('date')
            ->map(function ($items) {
                return $items
                    ->groupBy('room_group_id')
                    ->map(fn($group) => $group->sum('quantity'))
                    ->toArray();
            })
            ->toArray();
    }

    private function attributionsQuery(): Collection
    {
        if ($this->attributionData === null) {
            $this->attributionData = Attribution::query()->where(fn($q) => $q->whereIn('shoppable_id', $this->getRoomIds())->where('shoppable_type', OrderCartType::ACCOMMODATION->value))->get();
        }

        return $this->attributionData;
    }
}
