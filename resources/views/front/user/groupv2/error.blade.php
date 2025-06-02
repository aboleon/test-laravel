<x-front-logged-in-group-manager-v2-layout :event="$event">

    @if(isset($message))
        <x-mfw::alert :message="$message"/>

        @if (url()->previous() != url()->current())
            <a href="{{ url()->previous() }}" class="btn btn-dark">{{ __('front/event.back') }}</a>
        @endif
    @endif

</x-front-logged-in-group-manager-v2-layout>
