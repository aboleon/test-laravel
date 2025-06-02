<div class="modal fade"
     id="simpleModal"
     tabindex="-1"
     aria-labelledby="simpleModalLabel"
     data-ajax="{{route('ajax')}}"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="simpleModalLabel">Hey!</h1>
                <div class="ms-2 modal-spinner spinner-border spinner-border-sm" role="status"
                     style="display: none;"
                >
                    <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                What's up?
            </div>
            <div class="container mt-3">
                <div class="messages"></div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary close-button"
                        data-bs-dismiss="modal">{{ __('ui.close') }}
                </button>
                <button type="button" class="btn btn-primary action-button">Save changes</button>
            </div>
        </div>
    </div>
</div>
