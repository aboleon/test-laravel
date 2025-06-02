<div class="modal fade" id="reconfirmPreorderFailedModal" tabindex="-1" aria-labelledby="reconfirmPreorderFailedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="reconfirmPreorderFailedModalLabel">Attention</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body text-danger">
                Certaines informations de votre panier ont changé depuis votre dernière visite.
                Veuillez rafraîchir la page avant de continuer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}</button>
                <button onclick="location.reload()" type="button" class="btn btn-primary">Rafraîchir la page</button>
            </div>
        </div>
    </div>
</div>
