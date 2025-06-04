<x-backend-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Variables disponibles pour les courriers type
        </h2>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded mfw-form">
        <style>
            .variable-code {
                font-family: monospace;
                background-color: #f5f5f5;
                padding: 2px 4px;
                border-radius: 3px;
            }
            .test-info {
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .test-info strong {
                display: inline-block;
                min-width: 120px;
            }
            .event-selector {
                margin-bottom: 20px;
            }
        </style>

        <div id="ajax_container" data-ajax="{{ route('ajax') }}"></div>

        <!-- Event Selector -->
        <div class="event-selector">
            <form method="GET" action="{{ route('panel.mailtemplates.variables') }}" id="variablesForm" class="row g-3">
                <div class="col-md-5">
                    <label for="event_id" class="form-label">Sélectionner un événement :</label>
                    <select name="event_id" id="event_id" class="form-control">
                        <option value="">-- Sélectionner un événement --</option>
                        @foreach($events as $evt)
                            <option value="{{ $evt->id }}" {{ request('event_id', $event?->id) == $evt->id ? 'selected' : '' }}>
                                #{{ $evt->id }} - {{ $evt->texts?->name ?? 'Sans nom' }}
                                @if($evt->starts)
                                    ({{ $evt->starts }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5" id="eventContactSelectContainer">
                    <label for="event_contact_id" class="form-label">Sélectionner un contact :</label>
                    <select name="event_contact_id" id="event_contact_id" class="form-control">
                        <option value="">-- Sélectionner un contact --</option>
                        @if($eventContacts)
                            @foreach($eventContacts as $contact)
                                <option value="{{ $contact->id }}" {{ request('event_contact_id', $eventContact?->id) == $contact->id ? 'selected' : '' }}>
                                    #{{ $contact->id }} - {{ $contact->account?->first_name ?? '' }} {{ $contact->account?->last_name ?? 'Sans nom' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    @if(request('event_id') || request('event_contact_id'))
                        <a href="{{ route('panel.mailtemplates.variables') }}" class="btn btn-secondary w-100">Réinitialiser</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="test-info">
            <h4>Données de test utilisées :</h4>
            <table class="table table-sm">
                <tbody>
                @if($event)
                    <tr>
                        <th width="120"><strong>Événement</strong></th>
                        <td>
                            #{{ $event->id }} -
                            <a href="{{ route('panel.events.edit', $event->id) }}" target="_blank">
                                {{ $event->texts?->name ?? 'Sans nom' }}
                            </a>
                        </td>
                    </tr>
                @else
                    <tr>
                        <th width="120"><strong>Événement</strong></th>
                        <td><em>Aucun événement sélectionné</em></td>
                    </tr>
                @endif
                <tr>
                    <th width="120"><strong>Contact</strong></th>
                    <td>
                        @if($eventContact && $event)
                            #{{ $eventContact->id }} -
                            <a href="{{ route('panel.manager.event.event_contact.edit', [$event->id, $eventContact->id]) }}" target="_blank">
                                {{ $eventContact->account?->first_name ?? '' }} {{ $eventContact->account?->last_name ?? 'Sans nom' }}
                            </a>
                        @elseif($event)
                            <em>Aucun contact trouvé pour cet événement</em>
                        @else
                            <em>Sélectionnez un événement pour voir les contacts</em>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        @if($event)
            @foreach($tables as $table)
                <h4>{{ $table['title'] }}</h4>
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th width="35%">Label</th>
                        <th width="35%">Variable</th>
                        <th width="30%">Valeur</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($table['data'] as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td class="variable-code">{{ $row['variable'] }}</td>
                            <td>{!! $row['value'] !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        @else
            <div class="alert alert-info">
                Veuillez sélectionner un événement pour voir les variables disponibles et leurs valeurs de test.
            </div>
        @endif
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                // Handle event selection change
                $('#event_id').on('change', function() {
                    var eventId = $(this).val();

                    if (eventId) {
                        // Clear and disable the contact select
                        $('#event_contact_id').html('<option value="">Chargement...</option>').prop('disabled', true);

                        // Make AJAX call
                        ajax('action=getEventContactsForSelectedEvent&event_id=' + eventId + '&callback=setEventContactsForSelectedEvent', $('#ajax_container'));
                    } else {
                        // Clear the contact select if no event selected
                        $('#event_contact_id').html('<option value="">-- Sélectionner un contact --</option>');
                    }
                });

                // Handle contact selection change
                $('#event_contact_id').on('change', function() {
                    if ($(this).val()) {
                        $('#variablesForm').submit();
                    }
                });
            });
        </script>
    @endpush

    @push('callbacks')
        <script>
            function setEventContactsForSelectedEvent(data) {
                var selectHtml = '<option value="">-- Sélectionner un contact --</option>';

                if (data.contacts && typeof data.contacts === 'object') {
                    $.each(data.contacts, function(id, name) {
                        selectHtml += '<option value="' + id + '">' + name + '</option>';
                    });
                }

                $('#event_contact_id').html(selectHtml).prop('disabled', false);
            }
        </script>
    @endpush
</x-backend-layout>
