<div class="modal fade" id="checkoutSuccessModal" tabindex="-1" aria-labelledby="checkoutSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="checkoutSuccessModalLabel">Paiement réussi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                Le paiement a été effectué avec succès. Vous pouvez consulter votre commande dans votre espace membre.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}</button>
                <a href="{{route('front.event.group.orders', $event)}}" class="btn btn-primary">Voir mes commandes</a>
            </div>
        </div>
    </div>
</div>
