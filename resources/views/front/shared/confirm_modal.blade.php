<div class="modal fade"
     id="confirm_modal"
     aria-labelledby="confirm_modal_desc"
     aria-hidden="true"
     x-data
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 id="confirm_modal_desc"
                    class="modal-title">{{__('front/ui.confirm_action_title')}}</h5>


                <div class="spinner-border spinner-border-sm ajax-spinner ms-2"
                     role="status"
                     x-show="$store.confirm_modal.isLoading"
                >
                    <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">

                <div class="alert alert-danger"
                     x-show="$store.confirm_modal.errorMessage.length > 0">
                    <p x-text="$store.confirm_modal.errorMessage"></p>
                </div>

                <p>{{__('front/ui.are_you_sure_execute')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">{{__('front/ui.no')}}</button>
                <button type="button"
                        wire:model.live="modal_confirm"
                        @click="$store.confirm_modal.doConfirm()"
                        class="btn btn-danger">{{__('front/ui.yes')}}</button>
            </div>
        </div>
    </div>
</div>
@pushonce("js")
    <script>
      Alpine.store('confirm_modal', {
        isLoading: false,
        errorMessage: '',
        doConfirm: function() {
          this.isLoading = true;
          return this.confirm();
        },
        confirm: function() {
          alert('action confirmed (change me)');
        },
        close: function() {
          this.isLoading = false;
          let modalInstance = bootstrap.Modal.getInstance(document.getElementById('confirm_modal'));
          modalInstance.hide();
        },
        error: function(errMsg) {
          this.isLoading = false;
          this.errorMessage = errMsg;
        },
      });
    </script>
@endpushonce
