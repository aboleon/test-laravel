<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Récapitulatif</span>
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <x-back.topbar.edit-event-btn :event="$event" />
        </div>
    </x-slot>
    <div class="row mx-1 gy-4">
        <div class="col-md-6">
            @include('events.manager.dashboard.hotels')
            @include('events.manager.dashboard.deposits-grant')
            @include('events.manager.dashboard.unpaid-deposits-grant')
        </div>
        <div class="col-md-6">
            @include('events.manager.dashboard.services')
            @include('events.manager.dashboard.participants')
            @include('events.manager.dashboard.deposits-sellable')
        </div>
        {{--@include('events.manager.dashboard.deposits')--}}
       {{-- <div class="col-md-6">
            <div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded">
                @include('events.manager.grant.deposit.grant.index')
            </div>
        </div>--}}

    </div>

</x-event-manager-layout>
