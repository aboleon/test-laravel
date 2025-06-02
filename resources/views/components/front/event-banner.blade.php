@php use App\Accessors\EventAccessor; @endphp
@props([
    'event',
    'group' => 'banner_large',
    'default' => ''
])
@php
    $url = EventAccessor::getEventFrontUrl($event);
@endphp

<a class="d-block event-banner" href="{{$url}}">
    <x-mediaclass::printer :model="Mediaclass::forModel($event, $group)->first()"
                           :params="['style' => 'max-height: 140px;object-fit: cover;']"
                           :alt="$event->texts->name"
                           class="img-fluid"
                           :default="$default"
                           :responsive="false"/>
</a>
