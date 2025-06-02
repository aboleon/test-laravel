<div class="modal fade"
     id="simple_confirm_modal"
     aria-labelledby="simple_confirm_modal_desc"
     aria-hidden="true"
     data-ajax="{{route('ajax')}}"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 id="simple_confirm_modal_desc"
                    class="modal-title">{{__('front/ui.warning')}}</h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="messages"></div>
            <div class="modal-body">

                <div class="alert alert-info">
                    <p class="modal-confirm-text">
                        {{__('front/ui.are_you_sure_execute')}}
                    </p>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">{{__('front/ui.no')}}
                </button>
                <button type="button"
                        class="btn btn-danger btn-confirm">
                    {{__('front/ui.yes')}}
                    <div style="display: none;"
                         id="simple-confirm-modal-spinner"
                         class="spinner-border spinner-border-sm"
                         role="status">
                        <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
@pushonce("js")
    <script>
      $(document).ready(function() {

        if ('undefined' === typeof window.SimpleConfirmModal) {

          const jModal = $('#simple_confirm_modal');
          jModal.find('.btn-confirm').on('click', function() {
            if (window.SimpleConfirmModal._onConfirm) {
              window.SimpleConfirmModal._onConfirm();
            }
            window.SimpleConfirmModal.hide();
          });

          window.SimpleConfirmModal = {
            _onConfirm: null,
            show: function() {
              $('#simple_confirm_modal').modal('show');
              return this;
            },
            hide: function() {
              $('#simple_confirm_modal').modal('hide');
              return this;
            },
            setConfirmText: function(text) {
              $('#simple_confirm_modal .modal-confirm-text').html(text);
              return this;
            },
            onConfirm: function(callable) {
              this._onConfirm = callable;
              return this;
            },
          };
        }
      });
    </script>
@endpushonce
