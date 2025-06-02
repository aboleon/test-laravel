<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2>
            Gestion des avoirs
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <button type="button"
                    class="btn btn-secondary me-2"
                    data-bs-toggle="modal"
                    data-bs-target="#modal_export">
                <i class="bi bi-box-arrow-in-up-right"></i>
                Export global PDF
            </button>

            <a class="btn btn-sm btn-primary me-2"
               href="#">
                <i class="bi bi-box-arrow-in-up-right"></i>
                Exporter</a>
            <x-event-config-btn :event="$event"/>
            <div class="separator"></div>
        </div>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages/>
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')
    @include('invoices.modal.export-modal', ['action' => 'generateRefundExport'])
    @push('js')
        {{ $dataTable->scripts() }}
        <script src="{{ asset('js/orders/send_refund_from_modal.js') }}"></script>
    @endpush

</x-event-manager-layout>
