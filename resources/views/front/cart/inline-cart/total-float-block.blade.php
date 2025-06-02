@php
    use App\Helpers\Front\Cart\CartTotalsUtil;
    use MetaFramework\Accessors\Prices;

    $util = new CartTotalsUtil();
    $util->process((bool)$cart->pec_eligible, $cartLines);

@endphp

<div class="card card-body p-4 shadow sticky-top" style="top: 40px;">
    <h4 class="mb-3">{{ __('front/cart.inline_cart_total') }}</h4>
    <ul class="list-group list-group-borderless mb-2">
        @if($util->hasStays)
            <li class="list-group-item px-0 d-flex justify-content-between">
                <span class="h6 fw-light mb-0">{{ __('front/cart.inline_cart_accommodation') }}</span>
                <span class="h6 fw-light mb-0 fw-bold">
                    @if (($util->getAmount('stayTotalTtcWithPec') !== $util->getAmount('stayTotalTtcWithoutPec')) or $util->getAmount('amendableAmount'))
                        <span
                            class="text-decoration-line-through fw-normal fs-12">{{ $util->showAmount('stayTotalTtcWithoutPec') }}</span>
                    @endif

                    {{ Prices::readableFormat(price:$util->getAmount('stayTotalTtcWithPec')) }}

                </span>
            </li>
        @endif
        @if($util->hasServices)
            <li class="list-group-item px-0 d-flex justify-content-between">
                <span class="h6 fw-light mb-0">{{ __('front/cart.inline_cart_services') }}</span>
                <span class="h6 fw-light mb-0 fw-bold text-nowrap">
                    @if ($util->getAmount('serviceTotalTtcWithPec') !== $util->getAmount('serviceTotalTtcWithoutPec'))
                        <span
                            class="text-decoration-line-through fw-normal fs-12">{{ $util->showAmount('serviceTotalTtcWithoutPec') }}</span>
                    @else
                        {{ $util->showAmount('serviceTotalTtcWithoutPec') }}
                    @endif
                </span>
            </li>
        @endif
        @if($util->hasGrantDeposit)
            <li class="list-group-item px-0 d-flex justify-content-between">
                <span class="h6 fw-light mb-0">Prise en charge</span>
                <span
                    class="h6 fw-light mb-0 fw-bold text-nowrap">{{ $util->showAmount('totalTtcGrantWaiverFees') }}</span>
            </li>
        @endif
        <hr>
        <li class="list-group-item px-0 d-flex justify-content-between">
            <span class="h5 mb-0">{{ __('front/cart.inline_cart_total_to_pay') }}</span>
            <span class="h5 mb-0 text-nowrap">
                @if($util->getAmount('totalTtcWithPec') !== $util->getAmount('totalTtcWithoutPec'))
                    <span
                        class="text-decoration-line-through fw-normal fs-12">{{ $util->getAmount('totalTtcWithoutPec') }}</span>
                    {{ $util->showAmount('totalTtcWithPec') }}
                @else
                    {{ $util->showAmount('totalTtcWithoutPec') }}
                @endif
            </span>
        </li>
    </ul>

    @if($util->showDetails)
        <hr>
        <h6 class="mb-1">{{ __('front/cart.inline_cart_pay_details') }}</h6>
        <ul class="list-group list-group-borderless mb-2">
            <li class="list-group-item px-0 d-flex justify-content-between">
                <span class="h6 fw-light mb-0 small">{{ __('front/cart.inline_cart_total_net') }}</span>
                <span class="h6 fw-light mb-0 fw-bold small text-nowrap">
                    {{ Prices::readableFormat(price:$util->getAmount('detailsTotalNet')) }}</span>
            </li>
            <li class="list-group-item px-0 d-flex justify-content-between">
                <span class="h6 fw-light mb-0 small">{{ __('front/cart.inline_cart_tax_amount') }}</span>
                <span class="h6 fw-light mb-0 fw-bold small text-nowrap">
                    {{ Prices::readableFormat(price:$util->getAmount('detailsVatAmount')) }}</span>
            </li>

            @if($util->getAmount('nonTaxableTotal') > 0)
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="h6 fw-light mb-0 small">{{ __('front/order.no_vat') }} :</span>
                    <span
                        class="h6 fw-light mb-0 fw-bold small text-nowrap">{{ $util->showAmount('nonTaxableTotal') }}</span>
                </li>
            @endif
            <li class="list-group-item px-0 d-flex justify-content-between">
                <span class="h6 mb-0">{{ __('front/cart.inline_cart_gross_total') }}</span>
                <span class="h6 mb-0 text-nowrap">{{ $util->showAmount('totalTtcWithPec') }}</span>
            </li>
        </ul>
    @else
        @if($util->getAmount('nonTaxableTotal') > 0)
            <hr>
            <ul class="list-group list-group-borderless mb-2">
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <span class="h6 fw-light mb-0 small">{{ __('front/order.no_vat') }} :</span>
                    <span
                        class="h6 fw-light mb-0 fw-bold small text-nowrap">{{ $util->showAmount('nonTaxableTotal') }}</span>
                </li>
            </ul>
        @endif
    @endif

    <div x-cloak
         x-show="'payment' !== page"
         class="row justify-content-center mt-3">
        <div
            wire:click="finalizePayment"
            class="btn btn-primary w-auto">
            {{ __('front/cart.inline_cart_complete_order') }}
            <x-front.livewire-ajax-spinner space="" target="finalizePayment"/>
        </div>
    </div>

    @if($finalizePaymentError)
        <div class="alert alert-danger">
            {{ $finalizePaymentError }}
        </div>
    @endif
</div>
