<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Prestations</span> &raquo;
            <span>Configuration</span> &raquo;
            <span>{{ $data->title ?? '' }}</span>
        </h2>
        <x-back.topbar.edit-combo
                :event="$event"
                :create-route="route('panel.manager.event.sellable.create', $event)"
                :index-route="route('panel.manager.event.sellable.index', $event)"
                :model="$data"
                :delete-route="route('panel.manager.event.sellable.destroy', [$event, $data->id??'-1'])"
                :item-name="fn($m) => 'la prestation ' . $m->title"
        />

    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::validation-banner />
        <x-mfw::response-messages />

        @if ($errors->has('service_price.price.*'))
            <x-mfw::alert message="Les  prix doivent être tous des chiffres, au minium 0."/>
        @endif
        @if ($errors->has('service_price.ends.*'))
            <x-mfw::alert message="Les dates des prix doivent être toutes renseignées."/>
        @endif

        <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
            @csrf
            @if ($data->id)
                @method('PUT')
            @endif
            <x-mfw::tab-redirect />

            {{--
            casse le fonctionnement des tabs
            <x-tab-cookie-redirect le bid="event_sellable"
                                   selector="#event-sellable-nav-tab .mfw-tab" />
                                    --}}

            @include('events.manager.sellable.inc.tabs')
            @php
                $invitationQuantityEnabled = old('service.invitation_quantity_enabled', $data->id ? $data->invitation_quantity_enabled : 0);
            @endphp
            <div class="tab-content mt-3" id="nav-tabContent">
                @include('events.manager.sellable.tabs.config')
                @include('events.manager.sellable.tabs.texts')
                @include('events.manager.sellable.tabs.participations')
                @include('events.manager.sellable.tabs.inscriptions')
            </div>

        </form>
    </div>

    @push('js')
        <script>
            activateEventManagerLeftMenuItem('sellables');

            function checkboxManager() {
                return {
                    selected: [],
                    allChecked: false,
                    toggleAll() {
                        this.allChecked = !this.allChecked;
                        if (this.allChecked) {
                            this.selected = [...document.querySelectorAll('.inscriptions-table tbody input[type="checkbox"]')].map(checkbox => checkbox.value);
                        } else {
                            this.selected = [];
                        }
                    },
                    checkIfAllSelected() {
                        let checkboxes = document.querySelectorAll('.inscriptions-table tbody input[type="checkbox"]');
                        this.allChecked = [...checkboxes].every(checkbox => checkbox.checked);
                        this.selected = [...checkboxes].filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
                    },
                };
            }

        </script>
        <script src="{!! asset('js/dynamic_dictionnary.js') !!}"></script>
    @endpush

    @push('modals')
        @include('mfw-modals.launcher')
    @endpush

</x-event-manager-layout>
