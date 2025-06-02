<div class="modal fade"
     id="ajax-notif-modal"
     tabindex="-1"
     aria-labelledby="ajaxNotifModalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="ajaxNotifModalModalLabel">{{__('front/ui.warning')}}</h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('front/ui.close')}}</button>
            </div>
        </div>
    </div>
</div>

@push("js")
    <script>
      if ('undefined' === typeof window.AjaxNotifModal) {
        let jNotifModal = $('#ajax-notif-modal');
        let jMessageContainer = jNotifModal.find('.modal-body');
        let notifModal = new bootstrap.Modal(jNotifModal[0]);

        window.AjaxNotifModal = {
          messageStyle: 'alert',
          messagePrinter: function(status, ajax_messages, container, keepMessages) {
            notifModal.show();
            jMessageContainer.empty();
            $(ajax_messages).each(function(index, message) {
              $.each(message, function(key, value) {
                if ('text' === window.AjaxNotifModal.messageStyle) {
                  jMessageContainer.append('<div class="text-' + key + '">' + value + '</div>');
                } else {
                  jMessageContainer.append('<div class="alert alert-dismissible alert-' + key + '">' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('ui.close') }}"></button>' +
                    value + '</div>');
                }
              });
            });
          },
        };
      }
    </script>
@endpush
