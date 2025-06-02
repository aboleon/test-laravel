@if($grantWaiverFeesLines->count() > 0)

    @php
        // assuming we only have one line...
        $firstWaiverLine = $grantWaiverFeesLines->first();
    @endphp

    <div class="row">
        <div class="divine-secondary-color-text d-flex align-items-center fade show py-3 pe-2"
             role="alert">
            <i class="bi bi-person-lines-fill fa-fw me-1"></i>
            {{ __('front/order.pec') }}
        </div>
    </div>
    <div class="d-block">

        <div class="card row mb-2">
            <div class="card-body border">
                <div class="row border-bottom border-top border-light-subtle">
                    <div class="col-4 text-bg-light text-body">{{ __('front/order.deposit') }}</div>
                    <div class="col-8 text-dark">
                        {{$firstWaiverLine->total_ttc}} €
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
