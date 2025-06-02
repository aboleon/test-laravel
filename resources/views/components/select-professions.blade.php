@props([
    "event" => null,
])

@php
    $whiteListIds = null;
    if($event) {
        $whiteListIds = \App\Accessors\EventProfessions::getProfessionIdsByEventId($event->id);
    }
@endphp

<x-select-meta-dictionary
        key="professions"
        :attributes="$attributes->except('event')" :whiteListIds="$whiteListIds" />