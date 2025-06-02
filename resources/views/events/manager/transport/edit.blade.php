@php
    use App\Accessors\GroupAccessor;
    use App\Accessors\EventContactAccessor;
    use App\Enum\DesiredTransportManagement;
    use MetaFramework\Accessors\Prices;
@endphp
<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
        $account = $eventContact?->account;


        $eventContactId = $eventContact?->id??null;
        $interventionIds = [];
        if($eventContactId){
            \App\Models\EventManager\Program\EventProgramInterventionOrator::where('events_contacts_id', $eventContactId)->get()->each(function($item) use (&$interventionIds){
                $interventionIds[] = $item->event_program_intervention_id;
            });
        }


        $transportModel = $transport ?? (new App\Models\EventManager\Transport\EventTransport());


    @endphp

    <x-slot name="header">
        <h2 class="event-h2">

            <span>{{__('transport.transport')}}</span>
        </h2>

        <x-back.topbar.edit-combo
            :event="$event"
            :index-route="route('panel.manager.event.transport.index', $event)"
            :create-route="route('panel.manager.event.transport.create', $event)"
            :delete-route="route('panel.manager.event.transport.destroy', [$event, $transport?->id??'-1'])"
            :show-delete-btn="(bool)$transport?->id"
            :use-create-route="$eventContact?->id"
            :show-save-button="$eventContact"
            :model="$eventContact"
            item-name="le transport de {{ $eventContact?->user->names() }}"
        />

    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <div class="row m-3">
            <div class="col form" data-ajax="{{ route('ajax') }}">


                <x-mfw::validation-banner/>
                <x-mfw::response-messages/>

                <form method="post" action="{{ $route }}" id="wagaia-form">
                    @csrf
                    @if($formMethod)
                        @method($formMethod)
                    @endif

                    <div class="row pt-3">

                        @if($eventContact?->id && $interventionIds)
                            <x-orator-interventions :interventionIds="$interventionIds"/>
                        @endif

                        <div>
                            <!-- infos de base -->
                            @include('events.manager.transport.include.user_related')
                            <!-- cartes de fidélité et pièces identité -->
                            @include('events.manager.transport.include.fidelity_cards')
                        </div>

                        <!-- documents transport -->
                        @include('events.manager.transport.include.transport_documents')

                        <!-- transport aller -->
                        @include('events.manager.transport.include.arrival')

                        <!-- transport retour -->
                        @include('events.manager.transport.include.return')

                        @if ($transport?->id && $transport->desired_management == \App\Enum\DesiredTransportManagement::PARTICIPANT->value)
                            <div class="fs-4 mb-4 text-dark">
                                Total des
                                billets: {{ \MetaFramework\Accessors\Prices::readableFormat($transport?->ticket_price) }}
                            </div>
                        @endif


                        @if($transport?->id)
                            <!-- visible seulement par admin -->
                            @include('events.manager.transport.include.admin_related')
                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>

    @include('accounts.shared.dict_template')
    @push('modals')
        @include('mfw-modals.launcher')
    @endpush

    @push('callbacks')
        <script>
            function sendManagementMail() {
                $('button.send_management_mail').off().click(function () {
                    ajax('action=sendTransportManagementChangeFromModal&event_transport_id=' + $(this).data('model-id'), $('#mfw-simple-modal .modal-body'))
                });
            }
        </script>
    @endpush

    @push('js')
        <script src="{{ asset('js/eventmanager/transport_edit.js') }}"></script>
        <script>
            function onParticipantSelect(eventContactId) {
                jAutocompleteParticipantHiddenInput.val(eventContactId);
                window.location.href = "{{route('panel.manager.event.transport.editByEventContact', [$event, 'eventContactId'])}}".replace('eventContactId', eventContactId);

            }
        </script>
    @endpush

    @pushonce('js')
        <script src="{!! asset('js/dynamic_dictionnary.js') !!}"></script>
    @endpushonce

    <x-load-jquery-ui-autocomplete/>


</x-event-manager-layout>
