<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <h2 class="event-h2">

            <span>Grants</span>
        </h2>
        <div class="d-flex align-items-center gap-1" id="topbar-actions">
            <x-event-config-btn :event="$event" />
            <a class="btn btn-sm btn-secondary"
               href="{{ route('panel.manager.event.grants.index', $event) }}">
                <i class="fa-solid fa-bars"></i>
                Grants
            </a>
            <div class="separator"></div>
        </div>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts()}}
    @endpush
</x-event-manager-layout>
