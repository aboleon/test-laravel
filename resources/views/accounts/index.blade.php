<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{__('ui.contacts')}}
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
                        ->add(\App\Services\Filters\Data\AccountFilters::class)
                        ->add(\App\Services\Filters\Data\SharedAccountFilters::class);
            @endphp

            <x-saved-searches-bar
                :searchType="App\Enum\SavedSearches::CONTACTS->value"
                :searchFiltersProvider="$filterParams"
                :searchFilters="$searchFilters"
                ajaxContainerId="account-ajax-container"
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
                :create-route="route('panel.accounts.create', ['role' => $role])"
            />

        </div>
    </x-slot>

    @include('accounts.modal.action_panel')
    @include('accounts.modal.export_panel')

    <div class="wg-tabs nav nav-tabs">
        <a href="{{route('panel.accounts.index', $role)}}"
           class="nav-link tab @if(!$archived) active @endif">{{__('ui.active')}}</a>
        <a href="{{route('panel.accounts.archived', $role)}}"
           class="nav-link tab @if($archived) active @endif">{{__('ui.archived')}}</a>
    </div>

    <div id="account-ajax-container" data-ajax="{{route('ajax')}}">
        <div class="messages"></div>
    </div>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>

        <x-saved-search-alert :has-search-filters="(bool)$searchFilters"/>

        <x-datatables-mass-delete model="Account"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
        <script>
            removeTabCookieRedirect('contact');
        </script>
    @endpush
</x-backend-layout>
