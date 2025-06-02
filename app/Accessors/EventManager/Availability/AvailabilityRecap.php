<?php

namespace App\Accessors\EventManager\Availability;

use App\Accessors\EventManager\Availability;
use App\Enum\OrderClientType;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class AvailabilityRecap extends SubAccessor
{
    private array $bookingsData = [];

    public function __construct(public Availability $availability)
    {
        parent::__construct($this->availability);
        $this->compose();
    }

    public function get(?string $date = null, ?int $roomgroup = null): array
    {
        $date = $this->autoParseDate($date);

        if ($date && ! $roomgroup) {
            return $this->bookingsData[$date] ?? [];
        }

        if ($date && $roomgroup) {
            return $this->bookingsData[$date][$roomgroup] ?? [];
        }

        return $this->bookingsData;
    }

    protected function generate($date, $roomgroup=null): array
    {
        // Blocked rooms presence
        $on_quota = 0;


        // Group bookings by participation type and on-quota status
        $confirmedBookingsByType = $this->availability
            ->bookingsQuery()
            ->filter(fn($item) => $item->getRawOriginal('date') == $date && $item->room_group_id == $roomgroup)
            ->groupBy(['participation_type_id', 'on_quota'])
            ->mapWithKeys(fn($outerGroup, $participationTypeId)
                => [
                $participationTypeId => $outerGroup->sum(fn($innerGroup) => $innerGroup->sum('quantity')),
            ])
            ->toArray();

        $blocked_by_ptype = $this->availability->get('blocked.individual.'.$date.'.'.$roomgroup.'.participation_type') ?? [];

        $tempbooked_by_ptype    = $this->availability->get('booked.temp_by_participation_type.'.$date.'.'.$roomgroup) ?? [];
        $tempbooked_pec         = $this->availability->get('booked.temp_pec.'.$date.'.'.$roomgroup) ?? [];
        $temp_booked_for_groups = $this->availability->get('booked.temp_for_groups.'.$date) ?? [];

        $blocked_by_ptype_map = collect($this->availability->get('blocked.individual_by_participation_type'))
            ->mapWithKeys(fn($item, $key) => [$key => $item[$date][$roomgroup] ?? null])
            ->filter()
            ->all();


        $blocked_pec = $this->availability->get('blocked.grants.'.$date.'.'.$roomgroup, 0);

        $confirmed_groups_count = $this->sumGroupCounts($this->getGroupCounts($this->availability->get('booked.confirmed_groups_by_group'), $date, $roomgroup));

        // Group related
        $thisGroup = [];
        if ($this->availability->isGroup()) {
            $thisGroup
                = [
                'event_group_id' => $this->group_id,
                'blocked'        => $this->availability->get('blocked.groups_by_date_and_room_group.'.$date.'.'.$roomgroup.'.'.$this->group_id, 0),
                'temp'           => Arr::only($this->availability->get('booked.temp_for_groups.'.$date.'.'.$roomgroup.'.'.$this->group_id, ['on_quota' => 0, 'free' => 0]), ['on_quota', 'free']),
                'booked'         =>
                    $this->availability->get(
                        'booked.confirmed_groups_by_group.'.$this->group_id.'.'.$date.'.'.$roomgroup,
                        ['on_quota' => 0, 'free' => 0],
                    ),
            ];

            $remaining = 0;
            if ($thisGroup['blocked'] > 0) {
                $remaining = $thisGroup['blocked']
                    - ($thisGroup['temp']['on_quota'] ?? 0)
                    - $thisGroup['booked']['on_quota'];
            }
            $thisGroup['remaining'] = $remaining;

            $thisGroupRemainingQuota = $thisGroup['remaining'] ?? 0;
            $on_quota                = $thisGroupRemainingQuota > 0;
        } elseif ($this->availability->hasParticipationType()) {
            // Individual
            $on_quota = isset($blocked_by_ptype_map[$this->participation_type]) && $blocked_by_ptype_map[$this->participation_type] > 0;
        }


        $confirmed_groups_count['total'] = $confirmed_groups_count['on_quota'] + $confirmed_groups_count['free'];
        $temp_bookings                   = $this->availability->get('booked.temp.'.$date.'.'.$roomgroup) ?? 0;


        // Adapt calculations to use the new structure of $booked_by_ptype
        $confirmed_by_type = collect($confirmedBookingsByType)
            ->mapWithKeys(fn($item, $key) => [explode(',', $key)[0] => $item])
            ->toArray();

        $confirmed_by_blocked_ptype = array_intersect_key($confirmed_by_type, $blocked_by_ptype_map);
        $temp_by_blocked_ptype      = array_intersect_key($tempbooked_by_ptype, $blocked_by_ptype_map);


        $temp_by_type = array_intersect_key($tempbooked_by_ptype, array_flip($blocked_by_ptype));

        $amendedQuota   = $this->availability->generateAmendedQuota($date, $roomgroup);
        $cancelledQuota = $this->availability->generateCancelledQuota($date, $roomgroup);


        $groups_blocked                = $this->availability->get('blocked.groups_by_date_and_room_group.'.$date.'.'.$roomgroup, []);
        $temp_booked_for_groups_detail = $temp_booked_for_groups[$roomgroup] ?? [];

        $output = [
            'date'         => $date,
            'roomgroup'    => $roomgroup,
            'on_quota'     => (int)$on_quota,
            'this_group'   => $thisGroup,
            'is_grantable' => (int)$this->availability->accountIsGrantable(),
            'ptype'        => $this->availability->getParticipationType(),
            'blocked'      => [
                'keys'             => $blocked_by_ptype,
                'by_ptype'         => $blocked_by_ptype_map,
                'total_pec'        => $blocked_pec,
                'total_individual' => array_sum($blocked_by_ptype_map),
                'total_groups'     => array_sum($groups_blocked),
                'total'            => array_sum($blocked_by_ptype_map) + $blocked_pec + array_sum($groups_blocked),
                'groups'           => $groups_blocked,
            ],
            'confirmed'    => [
                'total'                  => $confirmed_groups_count['total'] + ($this->availability->get('booked.confirmed_individual.'.$date.'.'.$roomgroup.'.total') ?? 0),
                'total_individual'       => $this->availability->get('booked.confirmed_individual.'.$date.'.'.$roomgroup.'.total') ?? 0,
                'total_individual_quota' => collect($confirmed_by_blocked_ptype)->reject(fn($item, $key) => $key == 0)->sum(),
                'total_groups_quota'     => $confirmed_groups_count['on_quota'],
                'total_pec'              => ($this->availability->get('booked.grants.'.$date.'.'.$roomgroup) ?? 0),
                'groups'                 => $confirmed_groups_count,
                'by_ptype'               => $confirmed_by_type, // This now contains the grouped data
                'by_blocked_ptype'       => $confirmed_by_blocked_ptype,
            ],
            'temp'         => [
                'total'                  => $temp_bookings,
                'total_individual'       => $this->availability->getTempBookingsCount(OrderClientType::CONTACT->value),
                'total_individual_quota' => collect($temp_by_blocked_ptype)->filter(fn($value, $key) => in_array($key, $blocked_by_ptype))->sum(),
                'total_groups'           => $this->availability->getTempBookingsCount(OrderClientType::GROUP->value),
                'total_groups_quota'     => collect($temp_booked_for_groups_detail)->sum('on_quota'),
                'total_pec'              => array_sum($tempbooked_pec),
                'pec'                    => $tempbooked_pec,
                'groups'                 => $temp_booked_for_groups_detail,
                /*
                with participation type as key
                Array
                (
                    [4] => 1
                )
                */
                'by_ptype'               => $tempbooked_by_ptype,
                'by_blocked_ptype'       => $temp_by_type,
            ],
            'amended'      => $amendedQuota,
            'cancelled'    => $cancelledQuota,

        ];

        // PEC Delta
        $blocked_pec           = $output['blocked']['total_pec'];
        $total_pec_bookings    = $output['confirmed']['total_pec'] + $output['temp']['total_pec'];
        $pec_bookings_by_ptype = $this->availability
            ->bookingsQuery()
            ->filter(fn($item) => $item->getRawOriginal('date') == $date && $item->room_group_id == $roomgroup && $item->total_pec > 0)
            ->groupBy(['participation_type_id', 'on_quota'])
            ->mapWithKeys(fn($outerGroup, $participationTypeId)
                => [
                $participationTypeId => $outerGroup->sum(fn($innerGroup) => $innerGroup->sum('quantity')),
            ]);

        $pec_add      = 0;
        $summed_delta = 0;
        $pec_delta    = ($total_pec_bookings >= $blocked_pec) ? 0 : $blocked_pec - $total_pec_bookings;

        $pec_bookings_to_substract = min($total_pec_bookings, $blocked_pec);

        if ($blocked_pec) {
            $pec_add += $pec_bookings_to_substract;
            if ($output['is_grantable']) {
                $pec_add += $pec_delta;
            }


            $bookings_by_blocked_ptype     = collect($output['confirmed']['by_blocked_ptype'])->reject(fn($item, $key) => $key == 0);
            $pec_bookings_by_blocked_ptype = $pec_bookings_by_ptype->reject(fn($item, $key) => $key == 0);
            $summed_pec                    = $bookings_by_blocked_ptype->sum() + $pec_bookings_by_blocked_ptype->sum();

            if ($summed_pec > $blocked_pec) {
                $summed_delta = $summed_pec - $blocked_pec;
            }
        }


        $output['pec'] = [
            'blocked'                         => $blocked_pec,
            'bookings'                        => $total_pec_bookings,
            'pec_bookings_by_ptype'           => $pec_bookings_by_ptype->toArray(),
            'pec_temp_bookings_by_ptype'      => $output['temp']['pec'],
            'to_add'                          => $pec_add,
            'grantable_delta'                 => $pec_delta,
            'pec_bookings_to_substract'       => $pec_bookings_to_substract,
            'to_substract_from_blocked_ptype' => $summed_delta,
        ];


        return $output;
    }

    private function getGroupCounts(array $groupBookings, string $date, int $roomgroup): array
    {
        return collect($groupBookings)
            ->mapWithKeys(fn($item, $key)
                => isset($item[$date][$roomgroup]) ? [
                $key => [
                    'on_quota' => $item[$date][$roomgroup]['on_quota'] ?? 0,
                    'free'     => $item[$date][$roomgroup]['free'] ?? 0,
                ],
            ] : [])
            ->all();
    }

    private function sumGroupCounts(array $groupCounts): array
    {
        $filtered = collect($groupCounts)
            ->filter(fn($item) => isset($item['on_quota']) || isset($item['free']));

        if ($filtered->isEmpty()) {
            return ['on_quota' => 0, 'free' => 0];
        }

        return $filtered->reduce(
            fn($carry, $item)
                => [
                'on_quota' => ($carry['on_quota'] ?? 0) + ($item['on_quota'] ?? 0),
                'free'     => ($carry['free'] ?? 0) + ($item['free'] ?? 0),
            ],
            ['on_quota' => 0, 'free' => 0],
        );
    }


    protected function compose(): void
    {
        foreach ($this->availability->get('contingent') as $date => $contingent) {
            foreach ($contingent as $roomgroup => $stock) {
                $this->bookingsData[$date][$roomgroup] = $this->generate($date, $roomgroup);
            }
        }
    }

    public static function filterAbandondedByBlockedType(string $type, string $date, array $data, array $types): array
    {
        if ( ! in_array($type, ['amended', 'cancelled']) || ! $types) {
            return [];
        }

        $filtered = collect();

        foreach ($data as $roomGroup) {
            if (isset($roomGroup[$type]['orders'])) {
                $filtered = $filtered->merge($roomGroup[$type]['orders']);
            }
        }

        $filteredOrders = $filtered->filter(function ($order) use ($date, $types) {
            return $order['date'] === $date && in_array($order['participation_type_id'], $types);
        });

        return $filteredOrders
            ->groupBy('participation_type_id')
            ->map(function ($orders) {
                return $orders->sum('quantity');
            })
            ->toArray();
    }
}
