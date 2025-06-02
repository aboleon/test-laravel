<h5 class="mt-3">{{__('front/transport.step_1_departure')}}</h5>


@php
    $actionUrl = route('front.event.transport.update.participant.step.departure', $event);
    $title = __('front/transport.your_ticket');
@endphp
@include('front.user.transport.partials.departure-form')

<p class="mt-4 fw-bold text-danger">
    <i class="bi bi-exclamation-triangle me-1"></i>
    <i>
        {{__('front/transport.please_complete_your_request_to_get_reimbursement')}}
    </i>
</p>
