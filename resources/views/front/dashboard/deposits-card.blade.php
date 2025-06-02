@if ($grantDeposit)
<div class="card card-body shadow p-4 align-items-start">

    <h5 class="divine-main-color-text card-title mt-3 mb-2 d-flex w-100">
        <span>{{ trans_choice('front/ui.deposit', 2) }}</span>
    </h5>
    <div class="w-100">
            @php
                $grantType = $grantDeposit->shoppable_type == \App\Enum\OrderType::GRANTDEPOSIT->value ? 'grant' :  'service';
                $grantDepositPayment = $grantDeposit->order->payments->first();
                $isBilled = $grantDeposit->status == \App\Enum\EventDepositStatus::BILLED->value;
            @endphp
            <div class="row align-items-center">
                <div class="col-lg-6">
                    @if ($eventTimeline['days_to_event'] <= 30)
                        <h6 class="divine-secondary-color-text text-primary-emphasis m-0">
                            {{ $grantDeposit->shoppable_label }}
                        </h6>
                    @endif
                </div>
                <div class="col-lg-6 text-end">
                       <span
                       class="ms-3 smaller badge rounded-pill bg-dark ">{{ $grantType == 'grant' ? __('front/ui.grantdeposit') : __('front/ui.servicedeposit') }}</span>
                </div>
            </div>
            <p class="small pt-2 pb-0">
                @if ($isBilled)
                    {{ __('front/order.total_net') . ': ' .\MetaFramework\Accessors\Prices::readableFormat($grantDeposit->total_net, showDecimals: false). ', ' }}
                @endif
                {{ __('front/order.total_amount') . ': ' .\MetaFramework\Accessors\Prices::readableFormat($grantDeposit->total_net + $grantDeposit->total_vat,
            showDecimals: false) }}
                <br>
                @if ($grantDepositPayment)
                    {{ ucfirst(__('front/ui.'.($isBilled ? 'paid_at_f':'settled_at_f'), ['date' => $grantDepositPayment->date?->format('d/m/Y')])) }}
                @else
                    Unpaid
                @endif
            </p>
            <p class="small">

                {{ $grantType == 'grant' ? __('front/ui.granttext') : '' }}
            </p>
    </div>
</div>
@endif
