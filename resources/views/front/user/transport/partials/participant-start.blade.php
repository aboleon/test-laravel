<div x-show="'start' === page" x-cloak class="container">
    <h3 class="mb-4 p-2 bg-primary-subtle rounded-1">{{__('Mon transport')}}</h3>


    <p>{{__('front/transport.you_have_previously_indicated')}}</p>


    <div class="row">
        <div class="mb-3 col-12 col-md-6">
            <div class="card">
                <div class="card-body border">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               value="participant"
                               name="radio-transport-desired-management"
                               id="transport_mode1"
                               checked
                        >
                        <label class="form-check-label fw-bold text-dark"
                               for="transport_mode1">
                            {{__('front/transport.i_manage_my_transport')}}
                        </label>
                    </div>

                    <p class="mt-3 small">
                        {{$event->texts->transport_user}}
                    </p>

                </div>
            </div>
        </div>
    </div>


    @if($transport?->max_reimbursement)
        <p class="text-warning-emphasis p-2 mt-3 text-md-center">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{__('front/transport.max_reimbursement_text', ['amount' => $transport->max_reimbursement])}}
        </p>
    @elseif($event->texts->max_price_text)
        <p class="text-warning-emphasis p-2 mt-3 text-md-center">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{$event->texts->max_price_text}}
        </p>
    @endif
    @if($event->transport_tickets_limit_date)
        <p class="text-danger mt-3 text-start text-md-center">
            {{__('front/transport.transport_tickets_limit_date', ['date' => $event->transport_tickets_limit_date])}}
        </p>
    @endif


    <div class="row justify-content-center justify-content-md-end mt-3">
        <div @click="page='participant'" class="btn btn-primary w-auto">
            {{__('front/transport.deposit_my_invoices')}}
        </div>
    </div>
</div>
