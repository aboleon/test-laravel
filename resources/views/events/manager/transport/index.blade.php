<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <h2 class="event-h2">

            <span>{{ __('transport.transport') }}</span>
        </h2>
        <x-back.topbar.list-combo
            :event="$event"
            :create-route="route('panel.manager.event.transport.create', $event->id)"
        />
    </x-slot>

    @php
        $forOrators = in_array(\App\Enum\ParticipantType::ORATOR->value, (array)$event->transport);
        $forCongress = in_array(\App\Enum\ParticipantType::CONGRESS->value, (array)$event->transport);
        $forPec = in_array('pec', (array)$event->transport);
    @endphp


    <div class="shadow p-4 bg-body-tertiary rounded mb-4">
        <div class="d-sm-flex align-items-center justify-content-between text-black">
            <div class="d-sm-flex align-items-center text-black">

                <span class="ms-2 fw-bold d-flex align-items-center {{ !$forOrators ? 'opacity-50' : '' }}">
                    <i class="bi {{ $forOrators ? 'bi-check 6 text-success' : 'bi-check text-secondary' }} fs-3"></i> Orateurs
                </span>
                @if ($forOrators)
                    {!! in_array(\App\Enum\ParticipantType::ORATOR->value, (array)$event->transfert) ? '<span class="ps-1">+ transferts</span>' : '' !!}
                @endif

                <span class="ms-2 fw-bold d-flex align-items-center {{ !$forCongress ? 'opacity-50' : '' }}">
                    <i class="bi {{ $forCongress ? 'bi-check 6 text-success' : 'bi-check text-secondary' }} fs-3"></i>
                    Congressistes
                </span>
                @if ($forCongress)
                    {!! in_array(\App\Enum\ParticipantType::CONGRESS->value, (array)$event->transfert) ? '<span class="ps-1">+ transferts</span>' : '' !!}
                @endif

                <span class="ms-2 fw-bold d-flex align-items-center {{ !$forPec ? 'opacity-50' : '' }}">
                    <i class="bi {{ $forPec ? 'bi-check 6 text-success' : 'bi-check text-secondary' }} fs-3"></i>
                    PEC
                </span>
                @if ($forPec)
                    {!! in_array('pec', (array)$event->transfert) ? '<span class="ps-1">+ transferts</span>' : '' !!}
                @endif

            </div>
            <div class="d-sm-flex">
                <h6 class="p-0 mb-0 me-4 fw-bold">{{__('transport.ticket_total')}}</h6>
                <div class="me-4">{{\MetaFramework\Accessors\Prices::readableFormat($totals->total_before_tax ?? 0)}}
                    HT
                </div>
                <div>{{\MetaFramework\Accessors\Prices::readableFormat($totals->total_after_tax ?? 0)}} TTC</div>
            </div>
        </div>
    </div>

    @include('lib.datatable')

    @include('events.manager.transport.desired_datatable.index')
    @include('events.manager.transport.undesired_datatable.index')


</x-event-manager-layout>
