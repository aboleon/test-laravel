<div class="modal fade"
     id="checkoutErrorModal"
     tabindex="-1"
     aria-labelledby="checkoutErrorModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="checkoutErrorModalLabel">Paiement échoué</h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                Une erreur est survenue lors du paiement de votre commande. Veuillez réessayer, ou
                contactez-nous si le problème persiste.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
