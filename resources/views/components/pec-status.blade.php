@props(['event' => $event])
@if (!$event->pec?->is_active)
    <x-mfw::alert message="La prise en charge n'est pas activée pour cet évènement."/>
@endif
