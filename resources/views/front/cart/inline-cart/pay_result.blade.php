@php use App\Accessors\Order\Orders; @endphp
<div class="col-lg-8 mb-4 mb-sm-0">
    <div class="card card-body border p-4 shadow">
        <h4 class="mb-3">{{__('front/cart.payment_result')}}</h4>
        @if($paymentSuccessful)
            @php
                $lastOrder = Orders::getUserLastOrder($this->eventContact->user_id);
            @endphp
            @if (!$lastOrder)
                <x-mfw::alert :message="__('front/order.order_not_retrieved')"/>
            @else
                <div class="alert alert-success">
                    <p>
                        @if(Orders::orderContainsGrantDeposit($lastOrder))
                            {!! __('front/order.pec_paid') !!}<br>
                            <br>
                            <a href="{{route('front.event.dashboard', [
                            'locale' => app()->getLocale(),
                            'event' => $this->eventContact->event_id,
                        ])}}" class="btn btn-blue btn-sm">{{ __('front/order.pursue_shopping') }}</a>
                        @else
                            {{__('front/cart.order_accepted_notif')}}
                        @endif
                    </p>
                </div>
            @endif
        @else
            <div class="alert alert-danger">
                <p>{{$paymentError}}</p>
            </div>
        @endif
    </div>
</div>
