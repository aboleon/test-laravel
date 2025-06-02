<div class="wg-card">
    <header class="mb-3 d-flex justify-content-between align-items-start">

        <h4>Transports</h4>
        {{-- <a href="{{route('panel.manager.event.transport.create', $event)}}?participant_id={{$eventContact->id}}" --}}
        <a href="{{route('panel.manager.event.transport.create', ['event' => $event, 'event_contact' => $eventContact->id])}}"
           class="btn btn-sm btn-primary d-flex align-items-center gap-2 rounded-50">
            <i class="bi bi-plus-circle d-flex"></i>
        </a>
    </header>


    @if($hasTransport)
        {!! $transportDataTable->table()  !!}
        @push('js')
            {{ $transportDataTable->scripts() }}
        @endpush
    @else
        <p>Pas de transport pour ce participant</p>
    @endif

    <div class="mfw-line-separator my-3"></div>
</div>
