<x-front-logged-in-layout :event="$event">
    <x-front.session-notifs :event="$event" />

    <livewire:front.accommodation.accommodation-booker :event="$event->setRelations([])" :eventContact="$eventContact->setRelations([])" />

    <x-use-lightbox />
</x-front-logged-in-layout>
