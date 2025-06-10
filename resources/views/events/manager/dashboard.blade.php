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
            @include('events.manager.dashboard.services')
            @include('events.manager.dashboard.deposits-sellable')
            @include('events.manager.dashboard.deposits-grant')
        </div>
        <div class="col-md-6">
            @include('events.manager.dashboard.participants')
            <x-count-dashboard-v1 title="PEC attente de paiement de caution" :stats="$unpaidDepositsStats" />
            <x-count-dashboard-v1 title="PEC inscrits" :stats="$pecAndGrantDepositStats" />
            <x-count-dashboard-v2 title="Sans prestations" :stats="$eventContactsWhitoutAnyOrder" />
            <x-count-dashboard-v2 title="Groupes" :stats="['total' => $event->groups()->count()]" />
            @include('events.manager.dashboard.subs_by_family')

        </div>


    </div>

</x-event-manager-layout>
