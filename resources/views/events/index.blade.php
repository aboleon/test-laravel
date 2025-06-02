<x-backend-layout>
    <x-slot name="header">
        <h2>
            {{ trans_choice('events.label',2) }}
        </h2>

        <x-back.topbar.list-combo route-prefix="panel.events" />

    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="Event" name="texts.name"/>
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-backend-layout>
