@props([
    'amount' => 0,
    'event_id' => null,
])
@if($amount > 0 && $event_id)
    <div class="card card-body shadow p-4 align-items-start bg-success-subtle">
        <div class="d-flex gap-3 align-items-center">
            <h5 class="card-title mt-3 mb-2">{{__('front/dashboard.remaining_to_pay')}}</h5>
            <h3 class="card-title mt-3 mb-2 text-danger-emphasis">
                {{ \MetaFramework\Accessors\Prices::readableFormat(price:$amount, showDecimals: true) }}
            </h3>
        </div>
        <p>
            <a href="{{route('front.event.remaining-payments', [
                        'event' => $event_id,
                    ])}}" class="underline-hover"><i class="bi bi-arrow-right"></i> {{ __('front/order.make_payment') }}</a>
        </p>
    </div>
@endif
