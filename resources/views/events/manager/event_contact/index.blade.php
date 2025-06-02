@php
    use App\Enum\ParticipantType;
    $createParticipantType = "participant";
    if(ParticipantType::ORATOR->value === $groupType){
        $createParticipantType = ParticipantType::ORATOR->value;
    }
@endphp
<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            @php
                $topTitle = ParticipantType::translations()[$groupType] ?? 'Participants';
            @endphp
            <h2>
                {{ $topTitle }}
            </h2>
            @php
                $descriptions = [
                    route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'all']) => 'Pax reliés à l\'événement avec ou sans commande',
                    route('panel.manager.event.event_contact.index_with_order', ['event' => $event->id, 'group' => 'all', 'withOrder' => 1]) => 'Pax avec commande ou caution payée (PEC ou presta)',
                    route('panel.manager.event.event_contact.index_with_order', ['event' => $event->id, 'group' => 'all', 'withOrder' => 0]) => 'Pax sans commande',
                    route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'congress']) => 'Pax avec commande ou caution payée (PEC ou presta)',
                    route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'industry']) => 'Pax avec commande ou caution payée (PEC ou presta)',
                    route('panel.manager.event.event_contact.index', ['event' => $event->id, 'group' => 'orator']) => 'Pax avec commande ou caution payée (PEC ou presta)',
                    // route('panel.manager.event.pecorder.index', $event->id) => 'Pax avec commande PEC ou caution PEC payée'
                ];
            @endphp

            @if(isset($descriptions[url()->current()]))
                <span class="text-secondary">
                    {{ $descriptions[url()->current()] }}
                </span>
            @endif

            @if($group)
                <div>
                    du groupe
                    <a href="{{ route('panel.groups.edit', $group) }}">
                        {{ $group->name }}
                    </a>
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2" id="topbar-actions">
            <button type="button"
                    class="btn btn-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#modal_export_panel"
            >
                <i class="fa-solid fa-share-square"></i>
                Exporter
            </button>

            @php
                $filterParams = (new \App\Services\Filters\FilterParser())
                        ->setEventId($event->id)
                        ->add(\App\Services\Filters\Data\EventContactFilters::class)
                        ->add(\App\Services\Filters\Data\SharedAccountFilters::class);
            @endphp
            <x-saved-searches-bar
                :searchType="App\Enum\SavedSearches::EVENT_CONTACTS->value"
                :searchFiltersProvider="$filterParams"
                :search-filters="$searchFilters"
                ajaxContainerId="event-contact-ajax-container"
                :event_id="$event->id"
            />
            <div class="dropdown">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#modal_actions_panel">
                    <i class="fa-solid fa-cog"></i>
                    Actions
                </button>
            </div>

            <x-back.topbar.separator/>
            <x-back.topbar.list-combo
                :wrap="false"
                :event="$event"
                :show-create-route="false"
            />

            @if($groupType == 'all' || ParticipantType::ORATOR->value === $groupType)
                <button class="btn btn-sm btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modal_add_eventcontact_panel">
                    <i class="fa-solid fa-user-plus"></i>
                    Ajouter
                </button>
            @endif

        </div>
    </x-slot>

    @if($groupType == 'all')
        <div class="shadow p-4 mb-3 bg-body-tertiary rounded">

            <div class="counter d-flex align-items-center mb-3">
                <x-counter :count="$sessionCount[$groupType]" label="participants"/>
                <x-counter :count="$sessionCountwithOrder" label="inscrits"/>
                <x-counter :count="$sessionCountwithoutOrder" label="sans prestation"/>
                <x-counter :count="$sessionCountWebRegister[$groupType]" label="inscrits en ligne"/>
            </div>
        </div>
    @else
        <div class="shadow p-4 mb-3 bg-body-tertiary rounded">
            <div class="row">
                <div class="col col-md-3 text-center h5"><span
                        class="h3">{{ $sessionCount[$groupType] }}</span> {{$topTitle}}</div>
                <div class="col col-md-3 text-center h5"><span
                        class="h3">{{ $sessionCountWebRegister[$groupType] }}</span> inscrits en ligne
                </div>
            </div>
        </div>
    @endif

    @php
        $exportAction = "exportAccountProfilesByEventContact";
        $isParticipant = true;
    @endphp

    @include('accounts.modal.export_panel')
    @include('accounts.modal.action_panel', ['event_id' => $event->id])
    @include('accounts.modal.add_panel')

    <div id="event-contact-ajax-container" data-ajax="{{route('ajax')}}">
        <div class="messages"></div>
    </div>


    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>

        <x-saved-search-alert :has-search-filters="(bool)$searchFilters"/>

        <x-datatables-mass-delete
            model="event_contact"
            controller-path="EventManager\\EventContact\\EventContactController"
            model-path="EventContact"
            deleted-message="Le contact a été dissocié de cet événement."
        />
        @if (!$withOrder)
            <x-event-contacts-secondary-filter :secondary_filter="$secondaryFilter" :event_id="$event->id" :group="$groupType" />
        @endif
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}

        <script>
            $(document).ready(function () {
                // remove the tab cookie for edit page
                Cookies.set('mfw_tab_redirect_primary', 'dashboard-tabpane-tab', {expires: 1});
            });
        </script>

    @endpush

    @pushonce('js')
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
        <script src="{{ asset('js/eventmanager/send_event_confirmation.js') }}"></script>
    @endpushonce
    @include('lib.tinymce')
</x-event-manager-layout>
