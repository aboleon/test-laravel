<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ __('ui.groups') }}
        </h2>

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
                        ->add(\App\Services\Filters\Data\GroupFilters::class);
            @endphp

            <x-saved-searches-bar
                    :searchType="App\Enum\SavedSearches::GROUPS->value"
                    :searchFiltersProvider="$filterParams"
                    :search-filters="$searchFilters"
                    ajaxContainerId="groups-ajax-container"
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

            <x-back.topbar.separator />
            <x-back.topbar.list-combo routePrefix="panel.groups" :wrap="false" />
        </div>
    </x-slot>

    @include('groups.modal.export_panel')
    @include('groups.modal.action_panel')

    <div id="groups-ajax-container" data-ajax="{{route('ajax')}}">
        <div class="messages"></div>
    </div>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages />

        <x-saved-search-alert :has-search-filters="(bool)$searchFilters"/>

        <x-datatables-mass-delete model="Group" />
        <x-datatables-event-associator type="group"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

</x-backend-layout>
