<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.establishments.label',2) }}
        </h2>
        <x-back.topbar.list-combo route-prefix="panel.establishments" />

    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="Establishment"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-backend-layout>
