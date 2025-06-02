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
        </style>

        @foreach($tables as $table)
            <h4>{{ $table['title'] }}</h4>
            <table class="table table-sm">
                <thead>
                <tr>
                    <th width="30%">Label</th>
                    <th width="35%">Variable</th>
                    <th width="35%">Valeur</th>
                </tr>
                </thead>
                <tbody>
                @foreach($table['data'] as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="variable-code">{{ $row['variable'] }}</td>
                        <td>{{ $row['placeholder'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
</x-backend-layout>
