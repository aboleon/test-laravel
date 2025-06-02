<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Interventions</span>
            <span class="smaller"> ({{ $interventionCount }} interventions)</span>
        </h2>
        @php
            $createRouteParams = [
                'event' => $event,
            ];
            if (request()->has('session')) {
                $createRouteParams['session'] = request()->get('session');
            }
        @endphp
        <x-back.topbar.list-combo :event="$event"
                                  :create-route="route('panel.manager.event.program.intervention.create', $createRouteParams)" />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages />
        <x-datatables-mass-delete model="EventManager\Program\ProgramIntervention"
                                  name="title"
                                  question="<strong>Est-ce que vous confirmez la suppression des interventions sélectionnées?</strong>" />
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

</x-event-manager-layout>
