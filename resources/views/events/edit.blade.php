<x-backend-layout>
    @php
        $error = $errors->any();
    @endphp
    @push('css')
        <style>
            .participation_all {
                display: none;
            }
        </style>
    @endpush
    <x-slot name="header">
        <h2>
            {{ $data->id ? 'Édition' : 'Création' }}
            d'un {{ mb_strtolower(trans_choice('events.label',1)) }}
        </h2>
        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>

            @if ($data->id)
                <a href="{{ route('panel.manager.event.show', $data) }}"
                   class="btn btn-sm btn-secondary mx-2">
                    <i class="bi bi-bounding-box"></i> Gestion de l'èvènement
                </a>
            @endif

            <x-back.topbar.separator/>
            <x-back.topbar.edit-combo
                :wrap="false"
                route-prefix="panel.events"
                :model="$data"
                :item-name="fn($d) => 'l\'èvènement ' . $d->texts?->name"
                delete-btn-text="Archiver"
            />
        </div>
    </x-slot>

    <div class="shadow p-3 mb-5 bg-body-tertiary rounded">
        <div class="row m-3">
            <div class="col form" data-ajax="{{ route('ajax') }}">

                <x-mfw::validation-banner/>
                <x-mfw::validation-errors/>
                <x-mfw::response-messages/>

                <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
                    @if($data->id)
                        @method('put')
                    @else
                        <input type="hidden"
                               name="event[config][created_by]"
                               value="{{auth()->user()->id}}"/>
                    @endif
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $data?->id }}">
                    <x-mfw::tab-redirect/>
                    <fieldset class="position-relative">
                        <legend class="row">
                            <div class="col-sm-9">
                                {!! $printer->names() !!}
                            </div>
                            <div class="col-sm-3">
                                @if($data->id)
                                    <x-mfw::notice class="text-center"
                                                   message="Fiche créé par {{ App\Printers\UserRelated::creator($data) }}"/>
                                @endif
                            </div>
                        </legend>
                        <x-tab-cookie-redirect id="event" selector="#event-nav-tab .mfw-tab"/>
                        <nav class="d-flex justify-content-between" id="event-nav-tab">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <x-mfw::tab tag="general-tabpane" label="Fiche" :active="true"/>
                                <x-mfw::tab tag="contacts-tabpane" label="Clients"/>
                                <x-mfw::tab tag="config-tabpane" label="Configuration"/>
                                <x-mfw::tab tag="program-tabpane" label="Programme"/>
                                <x-mfw::tab tag="media-tabpane" label="Photos"/>
                                <x-mfw::tab tag="pec-tabpane" label="PEC"/>
                                <x-mfw::tab tag="shop-tabpane" label="Exposants"/>
                                <x-mfw::tab tag="transport-tabpane" label="Transport"/>
                                <x-mfw::tab tag="front-tabpane" label="Front"/>
                                <x-mfw::tab tag="sage-tabpane" label="SAGE"/>
                            </div>
                        </nav>
                        <div class="tab-content mt-3" id="nav-tabContent">
                            @include('events.tabs.general')
                            @include('events.tabs.contacts')
                            @include('events.tabs.config')
                            @include('events.tabs.program')
                            @include('events.tabs.media')
                            @include('events.tabs.pec')
                            @include('events.tabs.shop')
                            @include('events.tabs.transport')
                            @include('events.tabs.front')
                            {!! \App\Helpers\Sage::renderTab($data) !!}
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    @include('accounts.shared.dict_template')
    @push('modals')
        @include('mfw-modals.launcher')
    @endpush

    @push('js')
        <script>
            activateEventManagerLeftMenuItem('events');
        </script>
    @endpush

    @pushonce('js')
        {!! \App\Helpers\Sage::limitSageInput() !!}
        <script src="{!! asset('js/dynamic_dictionnary.js') !!}"></script>
        <script src="{!! asset('js/contacts.js') !!}"></script>
        <script src="{!! asset('js/events.js') !!}"></script>
    @endpushonce
</x-backend-layout>
