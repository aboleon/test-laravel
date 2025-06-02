<div class="modal fade"
     id="remainingPaymentsConfirmModal"
     tabindex="-1"
     aria-labelledby="remainingPaymentsConfirmModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" x-data="{
            cgvAccepted: false,
            attemptedToPay: false,
        }">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="remainingPaymentsConfirmModalLabel">
                    Confirmation</h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body" x-data="{ open: false}">
                <div class="form-check">

                    <input class="form-check-input"
                           type="checkbox"
                           x-model="cgvAccepted"
                           value=""
                           id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        J'accepte <a class="text-decoration-underline text-blue"
                                     href="#"
                                     x-on:click.prevent="open = !open"
                        >les conditions d'annulation et de remboursement</a>
                    </label>
                </div>


                <div x-cloak
                     x-transition
                     x-show="!cgvAccepted && attemptedToPay"
                     class="alert alert-danger mt-4">{{__('front/cart.you_must_accept_conditions_to_continue')}}
                </div>

                <div x-cloak x-show="open">
                    {{__('front/cart.cgv')}}
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.cancel') }}</button>
                <button x-cloak x-show="!cgvAccepted" @click="attemptedToPay=true" type="button" class="btn btn-primary">{{ __('ui.pursue') }}</button>
                <div x-cloak x-show="cgvAccepted" class="continue-button-container"></div>
            </div>
        </div>
    </div>
</div>
