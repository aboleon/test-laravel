<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Prestations Choix</span>
        </h2>
        <x-back.topbar.list-combo
                :event="$event"
                :create-route="route('panel.manager.event.choosable.create', $event)"
        />

    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        <x-datatables-mass-delete model="EventManager\Sellable\Choosable" name="title" question="<strong>Est-ce que vous confirmez la suppression des prestations séléctionnées?</strong>"/>
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

</x-event-manager-layout>
