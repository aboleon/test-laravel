<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2>
            Rooming list pour <a
                href="{{ route('panel.manager.event.accommodation.show', ['event'=>$event->id, 'accommodation' => $accommodation->id]) }}">
                {{ $accommodation->hotel->name }}
            </a>
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <a class="btn btn-sm btn-secondary me-2"
               href="{{ route('panel.manager.event.accommodation.show', ['event'=>$event->id, 'accommodation' => $accommodation->id]) }}">
                <i class="bi bi-arrow-left"></i>
                Retour sur l'hébergement</a>
            <a class="btn btn-sm btn-primary me-2"
               href="{{ route('panel.manager.event.accommodation.roominglist.export', ['event' => $event->id, 'accommodation' => $accommodation->id]) }}">
                <i class="bi bi-box-arrow-in-up-right"></i>
                Exporter</a>
            <x-event-config-btn :event="$event"/>
            <div class="separator"></div>
        </div>
    </x-slot>


    @include('reports.shared.rooming-list', ['data' => $data, 'roomingList' => $roomingList, 'with_order' => $with_order ?? false])

</x-event-manager-layout>
