@php use App\Accessors\EventAccessor;use App\Models\GenericMedia; @endphp
@props([
    'event',
    'group' => 'banner_large',
    'default' => ''
])
@php
    $url = EventAccessor::getEventFrontUrl($event);
    $default = Mediaclass::ghostUrl(GenericMedia::class, $group);
@endphp

<a class="d-block event-banner" href="{{$url}}">
    <x-mediaclass::printer :model="Mediaclass::forModel($event, $group)->first()"
                           :params="['style' => 'max-height: 140px;object-fit: cover;']"
                           :alt="$event->texts->name"
                           class="img-fluid"
                           :default="$default"
                           :responsive="false"/>
</a>
