<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Hébergements</span>
        </h2>


        <div class="d-flex align-items-center gap-2" id="topbar-actions">
            <x-back.topbar.list-combo
                :wrap="false"
                :event="$event"
                :show-create-route="false"
            />

            <button class="btn btn-sm btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#modal_add_accommodation_panel">
                <i class="fa-solid fa-hotel"></i>
                Ajouter
            </button>
        </div>


    </x-slot>

    @include('events.manager.accommodation.modal.add_panel')

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="EventManager\Accommodation"
                                  name="title"
                                  question="<strong>Est-ce que vous confirmez la suppression de l'association de cet hébergement ?</strong><p>Toutes les données relatives à l'asociation seront supprimées.</p>"/>
        {{--        <x-event-hotel-association :event="$event" />--}}
        {!! $dataTable->table()  !!}

        @php
            $hotels = \App\Models\EventManager\Accommodation::query()->where('event_id', $event->id)->with(['contingent'])->get()->sortBy(fn($sort) => $sort->hotel->name);
            $distinctDates = $hotels->flatMap(function($accommodation) {
            return collect($accommodation['contingent'] ?? [])
                ->pluck('date');
        })->unique()->sort()->values()->all();
            $distinctDatesCount = count($distinctDates);
        @endphp

        <style>
            .odd {
                background-color: #f3f3f3 !important;
            }
        </style>

        <table class="table table-responsive mt-5">
            <thead>
            <tr>
                <th></th>
                @foreach($distinctDates as $key => $dDate)
                    <th class="text-center {{ $key % 2 ? 'even' : 'odd' }}" colspan="4">{{ $dDate }}</th>
                @endforeach
            </tr>
            <tr>
                <th>Nom</th>
                @foreach($distinctDates as $dDate)
                    <th class="text-center">Restantes</th>
                    <th class="text-center">Commandées</th>
                    <th class="text-center">Bloquées non attribuées</th>
                    <th class="text-center">Total</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($hotels as $accommodation)
                @php
                    $availability = (new \App\Accessors\EventManager\Availability())->setEventAccommodation($accommodation);
                    $getAvailability = $availability->getAvailability();

                    $availability_recap = (new \App\Accessors\EventManager\Availability\AvailabilityRecap($availability));


                @endphp
                <tr>
                    <td>
                        {{ $accommodation->hotel->name }}
                        @for($i=0;$i<$accommodation->hotel->stars;++$i)
                            *
                        @endfor
                    </td>
                    @for($a=0;$a<count($distinctDates);++$a)
                        @php
                            $booked = 0;
                            $booked_from_blocked = 0;
                            $blocked = 0;
                            $attributed = 0;
                            $alternateCss = $a % 2 ? 'even' : 'odd';
                            $recap = $availability_recap->get($distinctDates[$a]);
                            $sqlDate = \Illuminate\Support\Carbon::createFromFormat('d/m/Y', $distinctDates[$a])->toDateString();
                            $attributed += $availability->get('attributions', [])['total_groups'][$sqlDate] ?? 0;

                            foreach ($recap as $roomgroup) {
                                $booked += $roomgroup['confirmed']['total']  - $roomgroup['cancelled']['total'] - $roomgroup['amended']['total'];
                                $booked_from_blocked += $roomgroup['confirmed']['total_individual_quota']
                                                + $roomgroup['confirmed']['total_groups_quota']
                                                + $roomgroup['temp']['total_individual_quota']
                                                + $roomgroup['temp']['total_groups_quota']
                                                + $roomgroup['pec']['to_add']
                                                - $roomgroup['pec']['to_substract_from_blocked_ptype'];
                                $blocked += $roomgroup['blocked']['total'];
                            }

                            $blocked_undistributed = $recap ? $blocked - $booked_from_blocked - $attributed : null;


//d($recap);
                        @endphp
                        <td class="text-center {{ $alternateCss }}">{{ $recap ? array_sum($getAvailability[$sqlDate]) : '' }}</td>
                        <td class="text-center {{ $alternateCss }}">{{  $recap ? $booked : '' }}</td>
                        <td class="text-center {{ $alternateCss }}">{{ !is_null($blocked_undistributed) ? $blocked_undistributed : '' }}</td>
                        <td class="text-center {{ $alternateCss }}">{{ $recap ? ($blocked_undistributed + $booked) : ''}}</td>
                    @endfor
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

</x-event-manager-layout>

