<div class="card">

    <div class="card-header zzbg-primary-subtle">
        <h4 class="mb-0 divine-secondary-color-text">{{__('front/cart.payment')}}</h4>
    </div>
    <div class="card-body border" x-data="{
        cgvAccepted: false,
        attemptedSubmit: false,
        attemptedToPay: false,
    }">
        @if($this->itemsTotalTtc > 0)
            <div class="form-check">
                <input class="form-check-input"
                       type="radio"
                       name="flexRadioDefault"
                       x-model="paymentMethod"
                       value="cb"
                       id="payment_method_cb">
                <label class="form-check-label" for="payment_method_cb">
                    {{ __('front/order.bank_card') }}
                </label>
            </div>
        @endif

        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   x-model="cgvAccepted"
                   value=""
                   id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                {!! __('front/cart.i_accept_reimbursement_conditions')!!}
            </label>
        </div>
        <div x-cloak
             x-transition
             x-show="!cgvAccepted && attemptedToPay"
             class="alert alert-danger mt-4">{{__('front/cart.you_must_accept_conditions_to_continue')}}
        </div>

        <div class="d-flex justify-content-end gap-2 mt-5">
            <button @click="page='cart'" class="btn btn-secondary btn-sm">
                {{__('front/cart.back_to_cart')}}
            </button>
            @if($payboxServerOk)
                {!! $payboxFormBegin !!}
                <button class="btn btn-primary btn-sm"
                        type="button"
                        @click="attemptedToPay = true; attemptedSubmit = true; if (!cgvAccepted) $event.preventDefault(); else $wire.recheckPreorder()"
                >
                    {{__('front/cart.pay')}}
                </button>
                <button class="d-none"
                        type="submit"
                        id="inline-cart-payment-button">{{ __('front/ui.validate') }}</button>
                {!! $payboxFormEnd !!}

            @else
                {!! __('front/order.payment_server_unavailable') !!}
            @endif
        </div>

    </div>
</div>
{{-- @include('front.user.cart.cgv-modal') --}}
