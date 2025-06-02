<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.hotels.label',2) }}
        </h2>
        <x-back.topbar.list-combo route-prefix="panel.hotels" />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="Hotel"/>
        <x-datatables-event-associator type="hotel"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-backend-layout>
