<div class="modal" id="livewire-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('front/ui.warning')}}</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="alert">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{__('front/ui.ok')}}
                </button>
            </div>
        </div>
    </div>
</div>
@pushonce("js")
    <script>
      $(document).ready(function() {
        Livewire.on('LivewireModal.show', function(response) {
          console.log('ee', response);
          let type = response[0];
          let message = response[1];
          let title = response[2];
          $('#livewire-modal').modal('show');
          if (title) {
            $('#livewire-modal').find('.modal-title').text(title);
          }
          $('#livewire-modal').find('.alert').addClass("alert-" + type).html(message);
        });
      });
    </script>
@endpushonce
