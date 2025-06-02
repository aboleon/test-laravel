@php use App\Accessors\Dates; @endphp
<h5 class="mt-3">{{__('front/transport.step_3_return')}}</h5>

@php
    $actionUrl = route('front.event.transport.update.divine.step.return', $event);
    $title = __('front/transport.your_wishes');
    $btnPrevious = 2;
@endphp
@include('front.user.transport.partials.return-form')
