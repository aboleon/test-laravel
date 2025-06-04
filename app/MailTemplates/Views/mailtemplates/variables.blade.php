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
        </style>

        <div class="test-info">
            <h4>Données de test utilisées :</h4>
                <table class="table table-sm">
                    <tbody>
                    @if($event)
                        <tr>
                            <th width="120">Événement</th>
                            <td>
                                #{{ $event->id }} -
                                <a href="{{ route('panel.events.edit', $event->id) }}" target="_blank">
                                    {{ $event->texts?->name ?? 'Sans nom' }}
                                </a>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th width="120">Contact</th>
                        <td>
                            @if($eventContact)
                                #{{ $eventContact->id }} -
                                <a href="{{ route('panel.manager.event.event_contact.edit', [$event->id, $eventContact->id]) }}" target="_blank">
                                    {{ $eventContact->account?->first_name ?? '' }} {{ $eventContact->account?->last_name ?? 'Sans nom' }}
                                </a>
                            @else
                                <em>Aucun contact trouvé pour cet événement</em>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

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
    </div>
</x-backend-layout>
