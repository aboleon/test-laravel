@php use App\Accessors\Dates; @endphp
<h5 class="mt-3">
    {{__('front/transport.step_2_departure')}}
</h5>

@php
    $actionUrl = route('front.event.transport.update.divine.step.departure', $event);
    $title = __("front/transport.your_wishes");
    $btnPrevious = 1;
@endphp
@include('front.user.transport.partials.departure-form')
