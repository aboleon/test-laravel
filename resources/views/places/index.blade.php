<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.places.label',2) }}
        </h2>
        <x-back.topbar.list-combo route-prefix="panel.places" />

    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="Place"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-backend-layout>>
