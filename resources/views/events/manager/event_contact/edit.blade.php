<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <h2 class="event-h2">
            <span>Participant</span>
        </h2>

        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>

            <x-back.topbar.edit-combo
                :wrap="false"
                :model="$eventContact"
                :event="$event"
                :delete-route="route('panel.manager.event.event_contact.destroy', [
                        'event' => $event,
                        'event_contact' => $eventContact,
                        ])"
                item-name="le participant {{ $eventContact->user->names() }} de l'événement"
                delete-btn-text="Dissocier"
                :index-route="route('panel.manager.event.event_contact.index', [
                        'event' => $event,
                        'group' => $eventContact->participationType?->group??'all',
                        ])"
                :use-create-route="false"
                :show-delete-btn="$eventContactAccessor->hasNothing()"

            />

        </div>
    </x-slot>

    @push("js")
        <script>
            /**
             * Important to keep this code BEFORE the call to x-tab-cookie-redirect.
             */
            $(document).ready(function () {
                const jWagaiaFormBtn = $('#topbar-actions [form="wagaia-form"]');

                $('#event_contact-nav-tab .mfw-tab').on('shown.bs.tab', function (e) {
                    let id = $(e.target).attr('id');
                    if ('general-tabpane-tab' === id) {
                        console.log('change form to form_event_contact_general');
                        jWagaiaFormBtn.attr('form', 'form_event_contact_general');
                    } else if ('dashboard-tabpane-tab' === id) {
                        console.log('change form to form_event_contact_dashboard');
                        jWagaiaFormBtn.attr('form', 'form_event_contact_dashboard');
                    } else if ('pec-tabpane-tab' === id) {
                        console.log('change form to form_event_contact_pec');
                        jWagaiaFormBtn.attr('form', 'form_event_contact_pec');
                    } else {
                        console.log('change form back to wagaia-form');
                        jWagaiaFormBtn.attr('form', 'wagaia-form');
                    }
                });
            });
        </script>
    @endpush

    @php
        $eventGroups = $eventContactAccessor->eventGroups();
        $userEventGroups = \App\DataTables\View\EventGroupContactView::where([
            'event_id' => $event->id,
            'user_id' => $eventContact->user_id
        ])->with('group')
        ->get()
        ->unique('event_group_id');

    @endphp

    <div class="shadow p-4 bg-body-tertiary rounded"
         data-ajax="{{route('ajax')}}"
         id="event-contact-main-container">
        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>
        <div class="row align-items-center text-black">

        <h3 class="col-md-6">

            <span>{{ $eventContact->account->names() }}</span>
        </h3>

            <div class="col-md-6 text-end">
                <span>Rattaché le {{ $eventContact->created_at->format('d/m/Y') }}
 /
                    @if($eventContact->profile->created_by != $eventContact->user_id)
                        Créé par {{ $eventContact->profile->creator->names() }}
                    @else
                        Enregistré en front
                    @endif
                </span>
            </div>
        </div>


        @if($userEventGroups->isNotEmpty())
            <b>Groupes</b>
            <div id="attached-event-groups" class="mt-2" style="border: 1px dotted #41739f;padding: 10px;">
                @foreach($userEventGroups as $eventGroup)
                    <div class="d-flex align-items-center group-{{ $eventGroup->event_group_id }}">
                        <a class="btn btn-sm btn-secondary"
                           target="_blank"
                           href="{{  route('panel.manager.event.event_group.edit', [
                                'event' => $event->id,
                                'event_group' => $eventGroup->event_group_id,
                            ]) }}">
                            {{ $eventGroup->group->name }}
                        </a>
                        <button type="button"
                                class="action-dissociate-contact-from-group btn btn-sm btn-link text-danger fs-5"
                                data-event-group-id="{{ $eventGroup->event_group_id }}">
                            <i class="bi bi-x-circle-fill"></i>
                            <div style="display:none"
                                 class="spinner-border spinner-border-sm"
                                 role="status">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
        <hr class="mb-4">

        <x-tab-cookie-redirect id="primary" selector="#event_contact-nav-tab .mfw-tab"/>
        @include('events.manager.event_contact.inc.tabs')

        <div class="tab-content mt-3" id="nav-tabContent">
            @include('events.manager.event_contact.tabs.dashboard')
            @include('events.manager.event_contact.tabs.general')
            @include('events.manager.event_contact.tabs.contact')
            @include('events.manager.event_contact.tabs.pec')
        </div>
    </div>


    @push("js")
        <script>
            $(document).ready(function () {
                $('.action-dissociate-contact-from-group').off().on('click', function () {
                    let eventGroupId = $(this).data('event-group-id'),
                        jSpinner = $(this).find('.spinner-border');
                    ajax('action=dissociateUserFromEventGroup&event_group_id=' + eventGroupId + '&user_id={{  $eventContact->user_id }}',
                        $('#event-contact-main-container'),
                        {
                            spinner: jSpinner,
                            successHandler: function () {
                                $('#attached-event-groups').find('.group-'+eventGroupId).remove();
                                return true;
                            }
                        });
                });
            });
        </script>

    @endpush


</x-event-manager-layout>
