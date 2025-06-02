@props([
    'event' => null,
    'text' => null,
])
<a {{$attributes}}
        href="{{route('front.event.show', [
            'locale' => app()->getLocale(),
            'event' => $event,
            'slug' => $event->texts->name,
        ])}}">{{$text??$slot}}</a>
