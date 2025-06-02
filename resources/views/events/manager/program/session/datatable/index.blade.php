<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Sessions</span>
            <span class="smaller"> ({{ $sessionCount }} sessions)</span>
        </h2>

        <x-back.topbar.list-combo :event="$event" :create-route="route('panel.manager.event.program.session.create', $event)"  />
    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="EventManager\Program\ProgramSession" name="title" question="<strong>Est-ce que vous confirmez la suppression des sessions sélectionnées?</strong>"/>
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')

    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

</x-event-manager-layout>
