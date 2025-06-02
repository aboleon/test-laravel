@php use App\Accessors\EventAccessor; @endphp
<x-front-layout>

    <div class="mt-3 text-center">
        <x-mfw::response-messages/>
    </div>
    {{--    @include ('front.sections.main-banner')--}}
    {{--    @include ('front.sections.counter')--}}
    @include ('front.sections.events')
    {{--    @include ('front.sections.action-box')--}}
    {{--    @include ('front.sections.reviews')--}}
</x-front-layout>
