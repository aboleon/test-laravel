<x-backend-layout>

    @push('css')
        <style>
            td.phone span {
                display: block;
            }
        </style>
    @endpush

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Courriers type</h2>
        <div class="d-flex align-items-center" id="topbar-actions">

            <a class="btn btn-sm btn-success me-2"
               href="{{ route('panel.mailtemplates.create') }}">Créer</a>
            <a class="btn btn-sm btn-secondary"
               href="{{ route('panel.mailtemplates.variables') }}">Test champs</a>
            <div class="separator"></div>

        </div>

    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-backend-layout>
