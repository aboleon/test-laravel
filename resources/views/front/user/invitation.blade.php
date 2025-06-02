<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs/>

    <div class="container position-relative">

        <h3 class="mb-4 p-2 divine-main-color-text zzbg-primary-subtle rounded-1">{{__('front/invitations.invitations')}}</h3>

        @foreach($invitations as $item)
            <livewire:front.invitation.invitation-item
                :event="$event->setRelations([])"
                :event-contact="$eventContact->setRelations([])"
                :item="$item"
                :key="$item->id"/>
        @endforeach

    </div>

    <x-use-lightbox/>

</x-front-logged-in-layout>
