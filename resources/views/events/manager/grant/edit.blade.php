<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Grants</span> &raquo; {{ $data->title }}
        </h2>

        <x-back.topbar.edit-combo
            :export="true"
            :event="$event"
            :model="$data"
            :item-name="fn($m) => 'le grant ' . $data->title"
            :delete-route="route('panel.manager.event.grants.destroy', [
                    'event' => $event,
                    'grant' => $data?->id??'-1',
                ])"
            :index-route="route('panel.manager.event.grants.index', $event)"
            :create-route="route('panel.manager.event.grants.create', $event)"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
            @csrf
            @if ($data->id)
                @method('PUT')
            @endif
            <x-mfw::tab-redirect/>

            <x-tab-cookie-redirect id="bo_em_grant" selector="#grant-nav-tab .mfw-tab"/>

            <nav class="d-flex justify-content-start mb-3">
                <div class="nav nav-tabs" id="grant-nav-tab" role="tablist">
                    @if ($data->id)
                        <x-mfw::tab tag="dashboard-tabpane" label="Dashboard" :active="true"/>
                    @endif
                    <x-mfw::tab tag="config-tabpane" label="Configuration" :active="!$data->id"/>
                    <x-mfw::tab tag="address-tabpane" label="Adresse facturation"/>
                    <x-mfw::tab tag="eligibility-tabpane" label="Éligibilité"/>
                </div>
                @if (isset($eventGrantView))
                    <span class="ms-5 d-flex justify-center align-content-center flex-wrap">{{ $eventGrantView->pax_count }} PEC</span>
                @endif
            </nav>

            <div class="tab-content mt-3 pt-2" id="nav-tabContent">
                @if ($data->id && isset($eventGrantView))
                    @include('events.manager.grant.tabs.dashboard')
                @endif
                @include('events.manager.grant.tabs.config')
                @include('events.manager.grant.tabs.address')
                @include('events.manager.grant.tabs.eligibility')
            </div>

        </form>
    </div>

    <div style="height:500px"></div>
    @push('js')
        <script>
            activateEventManagerLeftMenuItem('grants');
        </script>
    @endpush

</x-event-manager-layout>
