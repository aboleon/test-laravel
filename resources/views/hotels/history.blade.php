<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.hotels.label',2) }} Historique
        </h2>
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
