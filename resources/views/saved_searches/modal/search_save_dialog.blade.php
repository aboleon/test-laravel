@php
    use App\Enum\SavedSearches;
@endphp
<div class="modal fade"
     id="modal_search_save_dialog"
     tabindex="-1"
     aria-labelledby="the_search_save_dialog"
     aria-hidden="true">
    <div class="modal-dialog">
        <form id="modal-search-save-dialog-ajax-form"
              class="modal-form"
              data-ajax="{{route('ajax')}}">

            <input type="hidden" name="type" value="{{$searchType}}" />
            @if($currentSearchId)
                <input type="hidden" name="id" value="{{$currentSearchId}}" />
            @endif

            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="the_search_save_dialog">
                        @if($currentSearchId)
                            Modifier
                        @else
                            Sauvegarder
                        @endif
                        la
                        recherche</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="search_save_dialog_name" class="form-label">Nom de la recherche</label>
                        <input type="text"
                               class="form-control"
                               id="search_save_dialog_name"
                               name="name"
                               value="{{$currentSearchName??''}}">
                    </div>
                </div>


                <div class="messages m-3"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" class="btn btn-primary submit-btn">Sauvegarder</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script>

      $(document).ready(function() {

        $('#modal_search_save_dialog').on('shown.bs.modal', function(e) {
          $('.modal-backdrop').hide(); // bug modal backdrop still showing
        });


        const jAjaxForm = $('#modal-search-save-dialog-ajax-form');
        jAjaxForm.find('.submit-btn').on('click', function(e) {
          e.preventDefault();

          const serializedData = 'action=saveSavedSearch&' + jAjaxForm.serialize();
          ajax(serializedData, jAjaxForm, {
            successHandler: function(response) {
              if (response.id) {
                // utils.reloadWithQueryParams({saved_search_id: response.id});
                utils.reload();
              }
              return true;
            },
          });

        });
      });
    </script>
@endpush


@pushonce('js')
    <script src="{!! asset('js/utils.js') !!}"></script>
@endpushonce
