@php use App\Accessors\EventAccessor; @endphp
<x-front-layout :event="$event">
    <div class="mt-5">
        <h1>{{$event->texts->privacy_policy_title}}</h1>
        <p>
            {{$event->texts->privacy_policy_text}}
        </p>
    </div>

</x-front-layout>
