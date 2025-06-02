@php
    use App\Models\EventManager\EventGroup;
    use App\Models\EventTexts;

    $eventsQuery = EventTexts::query()
    ->join('events', 'events_texts.event_id', '=', 'events.id')
    ->whereIn('events_texts.event_id', EventGroup::select('event_id')->where('group_id', $data->id))
    ->whereNull('events.deleted_at')
    ->select('events_texts.name', 'events_texts.event_id');

    $events = $eventsQuery->pluck('name', 'event_id');
@endphp

@if ($events->isNotEmpty())

    @foreach($events as $event_id => $event)
        <a class="d-block"
           href="{{ route('panel.manager.event.show', $event_id) }}">{!! $event !!}</a>
    @endforeach

@endif
