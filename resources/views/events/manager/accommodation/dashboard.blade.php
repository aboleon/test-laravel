<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2 fs-4">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Récap</span>
        </h2>

        <div class="d-flex align-items-center gap-1" id="topbar-actions">

            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    Rooming liste
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <i class="bi bi-search position-absolute"></i>
                        <a class="dropdown-item"
                           href="{{ route('panel.manager.event.accommodation.roominglist.report', ['event' => $event, 'accommodation' => $accommodation]) }}"
                           style="font-size: 15px;text-indent: 40px">
                            Consulter</a></li>
                    <li>
                        <i class="bi bi-box-arrow-in-up-right m-0 position-absolute" style="padding-bottom: 2px"></i>
                        <a class="dropdown-item"
                           href="{{ route('panel.manager.event.accommodation.roominglist.export', ['event' => $event, 'accommodation' => $accommodation]) }}"
                           style="font-size: 15px;text-indent: 40px">
                            Exporter</a></li>
                </ul>
            </div>


            <a class="btn btn-sm btn-secondary" href="{{ route('panel.manager.event.accommodation.index', $event) }}">
                <i class="fa-solid fa-bars"></i>
                Index
            </a>

            <div class="separator"></div>

            <x-event-config-btn :event="$event"/>


            <x-save-btns/>

            <div class="separator"></div>
        </div>

    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded wg-card">
        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        @include('events.manager.accommodation.tabs')
        <h4 class="mt-5 mb-4">Chambres</h4>
        <div class="mfw-line-separator mb-4"></div>

        @include('events.manager.accommodation.inc.general_recap')

        <h4 class="my-5 mb-4">Récap global</h4>
        <div class="mfw-line-separator mb-4"></div>

        @php
            $availability_recap = (new \App\Accessors\EventManager\Availability\AvailabilityRecap($availability));
        @endphp

        @foreach($availability->get('contingent') as $date => $contingent)
            <div class="row contingent-row mb-4 text-dark mfw-line-separator">
                <div class="col-sm-2 text-center">
                    <div>
                <span class="fw-bold d-block fs-6 mb-2">
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}
                </span>
                        <span class="mfw-badge mfw-bg-red">{{ array_sum($contingent) }}</span>
                    </div>
                </div>
                <div class="col-sm-8">
                    @php
                        $recap = collect($availability_recap->get($date));

                        // Debugging: Uncomment if needed
                        // d($recap, '$recap for ' . $date);

                        // Calculate bookings by blocked type
                        $recap_by_blocked_type =
                            $recap->sum(fn($item) => array_sum($item['confirmed']['by_blocked_ptype']))
                            + $recap->sum(fn($item) => array_sum($item['temp']['by_blocked_ptype']));

                        // Calculate total bookings (confirmed + temp)
                        $recap_all = $recap->sum('confirmed.individual')
                            + $recap->sum('temp.total')
                            + ($recap->sum('temp.groups.on_quota') + $recap->sum('temp.groups.free'));

                        $delta = $recap->sum('delta.total');
                        $blocked_quota_bookings = 0;
                        $blocked = $availability->get('blocked')['individual'][$date] ?? [];
                        $booked_g = [];
                        $blocked_g = $availability->get('blocked')['groups_by_date_and_room_group'][$date] ?? [];
                        $temp_booked = $availability->get('booked')['temp'][$date] ?? [];
                        $booked = $availability->get('booked')['confirmed_individual'][$date] ?? [];

                        $booked_by_type = $availability->get('booked')['confirmed_by_participation_type'][$date] ?? [];
                        $cancelled_by_type = $recap->sum(fn($item) => $item['cancelled']['total']);
                        $amended_by_type = $recap->sum(fn($item) => $item['amended']['total']);

                        $temp_booked_by_type = $availability->get('booked')['temp_by_participation_type'][$date] ?? [];
                        $recap_groups = $availability->get('booked')['confirmed_groups_by_date'][$date] ?? [];
                        $pec_by_type = $availability->get('booked')['confirmed_by_participation_type_pec'][$date] ?? [];
                        $totalCancelledAndAmended = $recap->sum('amended.total') + $recap->sum('cancelled.total');

                        $total_abandonned_by_blocked_type = 0;
                    @endphp

                    @if($recap->sum('blocked.total'))
                        <b class="pb-0 d-block">Chambres bloquées depuis l'hôtel</b>
                        @php
                            $total = $recap->sum('blocked.total_individual') + $recap->sum('blocked.total_pec') + $recap->sum('blocked.total_groups');
                            $grant = $recap->sum('blocked.total_pec');
                            $translated_ptypes = $participations->pluck('name', 'id')->sort()->toArray();
                            $participationTypes = collect($blocked)->pluck('participation_type')->flatten()->unique()->filter();

                            $flattened_booked_by_type = collect($booked_by_type)->reduce(fn($carry, $item) => $carry + $item, []);
                            $flattened_temp_booked_by_type = collect($temp_booked_by_type)->reduce(fn($carry, $item) => $carry + $item, []);

                            $participationTypes_bookings = array_intersect_key($flattened_booked_by_type, array_flip($participationTypes->toArray()));
                            $flattened_pec = collect($pec_by_type)->reduce(fn($carry, $item) => $carry + $item, []);
                            $participationTypes_pec = array_intersect_key($flattened_pec, array_flip($participationTypes->toArray()));

                            $cancelled_by_blocked_type = \App\Accessors\EventManager\Availability\AvailabilityRecap::filterAbandondedByBlockedType(
                                type: 'cancelled',
                                date: $date,
                                data: $recap->toArray(),
                                types: $participationTypes->toArray()
                            );

                            $amended_by_blocked_type = \App\Accessors\EventManager\Availability\AvailabilityRecap::filterAbandondedByBlockedType(
                                type: 'amended',
                                date: $date,
                                data: $recap->toArray(),
                                types: $participationTypes->toArray()
                            );

                            $total_abandoned = (array_sum($cancelled_by_blocked_type) + array_sum($amended_by_blocked_type));
                            $total_abandonned_by_blocked_type += $total_abandoned;

                            $interm_total = $recap_by_blocked_type - $total_abandoned;
                        @endphp

                        @if ($total)
                            {{-- {{ $participationTypes->map(fn($item) => $translated_ptypes[$item])->sort()->join(', ') }} :--}}
                            <br>{{ $delta < 0 ? $interm_total + $delta : $interm_total }}
                            /{{ $total }} bloquées
                            @if($grant)
                                dont {{ $recap->sum('pec.pec_bookings_to_substract') }}
                                / {{ $grant }} bloquées pour GRANT
                            @endif
                            <br>
                        @endif
                    @endif

                    @if ($blocked_g)
                        <b class="pt-2 d-block">Chambres bloquées depuis les fiches groupes</b>
                        @php
                            $flattened = collect($blocked_g)->reduce(fn($carry, $item) => $carry + $item, []);
                            $booked_g = $availability->get('booked')['confirmed_groups_by_date'][$date] ?? [];
                        @endphp
                        @foreach($flattened as $group => $total)
                            {{ $eventGroups[$group] . ' : ' . (array_key_exists($group, $booked_g) ? collect($booked_g[$group])->pluck('on_quota')->sum() : 0) }}
                            /{{ $total }}
                            bloquées<br>
                        @endforeach
                    @endif

                    <b class="pt-2 d-block">Résas libres</b>
                    @php
                        $group_free = $recap->pluck('confirmed.groups.free')->sum();
                        $total_temp = $recap->pluck('temp.total')->sum();
                        $temp_individual = $recap->pluck('temp.total_individual')->sum();
                        $temp_group_free = $recap->pluck('temp.groups.free')->sum();
                        $total_blocked_individual = $recap->pluck('confirmed.total_individual_quota')->sum();
                        $total_individual = $recap->pluck('confirmed.total_individual')->sum() - $total_blocked_individual;

                        // Subtract cancelled individual bookings (free only)
                        $cancelled_individual_free = $recap->sum(fn($item) => $item['cancelled']['free']);
                        $amended_individual_free = $recap->sum(fn($item) => $item['amended']['free']);
                        $adjusted_total_individual = $total_individual - $cancelled_individual_free - $amended_individual_free;
                    @endphp
                    Individuels: {{ $adjusted_total_individual . ($temp_individual > 0 ? ' (+ ' . $temp_individual . ' en attente)' : '') }}
                    <br>
                    Groupes: {{ $group_free . ($temp_group_free > 0 ? ' (+ ' . $temp_group_free . ' en attente)' : '') }}

                    <p class="mt-2">
                        <b>Total résas :
                            {{ ($total_individual + $recap->pluck('confirmed.groups.total')->sum()) - $totalCancelledAndAmended + $total_blocked_individual
                            . ($total_temp > 0 ? ' (+ ' . $total_temp . ' en attente)' : '') }}
                            <br>
                            Restant : {{ array_sum($availability->getAvailability()[$date]) }}
                        </b>
                    </p>
                </div>
            </div>
            @php
                $booked_g = [];
            @endphp
        @endforeach
    </div>
</x-event-manager-layout>
