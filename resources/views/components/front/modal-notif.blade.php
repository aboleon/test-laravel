@props([
    'title' => 'Attention',
    'text' => '',
    'type' => 'info',
])

<div class="modal"
     id="notifModal"
     tabindex="-1"
     aria-labelledby="notifModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="notifModalLabel">{{$title}}</h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-{{$type}}">
                    {{$text}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@pushonce("js")
    <script>
      $(document).ready(function() {
        $('#notifModal').modal('show');
      });
    </script>
@endpushonce
