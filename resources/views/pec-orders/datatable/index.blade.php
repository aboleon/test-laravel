<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            <h2>
                PEC
            </h2>
    
            <span class="text-secondary">
                Pax avec commande PEC ou caution PEC payée
            </span>
        </div>
        

        <div class="d-flex align-items-center" id="topbar-actions">
            <a class="btn btn-sm btn-primary me-2"
               href="#">
                <i class="bi bi-box-arrow-in-up-right"></i>
                Exporter</a>
            <x-event-config-btn :event="$event"/>

            <div class="separator"></div>

            <a class="btn btn-sm btn-success ms-2"
               href="{{ route('panel.manager.event.orders.create', $event) }}">
                <i class="fa-solid fa-circle-plus"></i>
                Commande
            </a>
            <a class="btn btn-sm btn-warning ms-2 text-dark"
               href="{{ route('panel.manager.event.orders.create', ['event' => $event->id, 'as_orator']) }}">
                <i class="fa-solid fa-circle-plus"></i>
                Commande intervenant
            </a>

            <div class="separator"></div>
        </div>

    </x-slot>

    @if ($grant_count >0 & $grant_count < 2)
        <x-mfw::notice class="mb-4" message="Cet évènement a un seul GRANT configuré"/>
    @endif


    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        {!! $dataTable->table()  !!}
    </div>

    @push('css')
        <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.3.6/r-2.4.1/datatables.min.css"
              rel="stylesheet"/>
    @endpush
    @push('js')
        <script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.3.6/r-2.4.1/datatables.min.js"></script>
        {{ $dataTable->scripts() }}
    @endpush

    @include('pec-orders.datatable.modal')
</x-event-manager-layout>
