<h5 class="mt-3">{{__('front/transport.step_2_return')}}</h5>

@php
    $actionUrl = route('front.event.transport.update.participant.step.return', $event);
    $title = __('front/transport.your_ticket');
    $btnPrevious = 1;
@endphp
@include('front.user.transport.partials.return-form')

<p class="mt-4 fw-bold text-danger">
    <i class="bi bi-exclamation-triangle me-1"></i>
    <i>
        {{__('front/transport.please_complete_your_request_to_get_reimbursement')}}
    </i>
</p>
