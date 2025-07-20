@props([
    'event' => null
])

@php
    $defaultThumbnail = Mediaclass::ghostUrl(\App\Models\GenericMedia::class, 'thumbnail');
@endphp

@if(isset($event) && $event instanceof \App\Models\Event)

    <x-mediaclass::printer :model="Mediaclass::forModel($event, 'thumbnail')->first()"
                           :alt="$event->texts->name"
                           class="img-fluid"
                           :default="$defaultThumbnail"
                           :responsive="false"/>

@else
    <img src="{{ $defaultThumbnail }}" alt="{{ config('app.name') }}" class="img-fluid">
@endif
