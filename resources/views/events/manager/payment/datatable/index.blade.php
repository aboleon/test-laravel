<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">

            <span>Paiements</span>
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <a class="btn btn-sm btn-primary me-2"
               href="#">
                <i class="bi bi-box-arrow-in-up-right"></i>
                Exporter</a>
            <x-event-config-btn :event="$event"/>
            <div class="separator"></div>
        </div>
    </x-slot>
    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages />
        <x-datatables-mass-delete model="EventManager\Payment\EventPayment"
                                  name="title"
                                  question="<strong>Est-ce que vous confirmez la suppression des paiements sélectionnés?</strong>" />
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')

    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

</x-event-manager-layout>
