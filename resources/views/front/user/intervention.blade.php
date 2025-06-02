@php
    $account = $user->account;
@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />

    <div class="container position-relative">

        <h3 class="mb-4 p-2 divine-main-color-text zzbg-primary-subtle rounded-1">{{__('front/interventions.interventions')}}</h3>
        @foreach($items as $item)
            <livewire:front.intervention.participant-item :event="$event->setRelations([])"
                                                          :item="$item"
                                                          :key="$item->id" />
        @endforeach

    </div>

    <x-use-lightbox />

</x-front-logged-in-layout>
