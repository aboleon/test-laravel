<x-front-logged-in-layout :event="$event">
    <x-front.session-notifs :event="$event"/>

    <livewire:front.accommodation.accommodation-booker
        :event="$event->setRelations([])"
        :eventContact="$eventContact->setRelations([])"
        :amend="$amend"
        :amendable="$amendable"/>

    @push('js')
        @if ($amend)
            <script>
                setTimeout(function () {
                    $(function () {
                        $('#search-accommodation').trigger('click');
                    });
                }, 500);
            </script>
        @endif
    @endpush

    <x-use-lightbox/>

</x-front-logged-in-layout>
