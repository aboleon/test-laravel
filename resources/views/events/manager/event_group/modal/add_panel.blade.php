@php use App\Accessors\ParticipationTypes;use App\Enum\ParticipantType; @endphp
<div class="modal fade"
     id="modal_add_eventgroup_panel"
     tabindex="-1"
     aria-labelledby="modal_add_eventgroup_panel_label"
     aria-hidden="true">
    <div class="modal-dialog big-select2-context">
        <form class="modal-form" data-ajax="{{route('ajax')}}">


            <div class="modal-content">
                <div class="modal-header d-flex align-items-end">
                    <h1 class="modal-title fs-5" id="modal_add_eventgroup_panel_label">
                        Ajouter / Créer</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-danger select-group-error"
                         style="display: none">
                        Veuillez sélectionner un groupe.
                        <br> Si vous ne trouvez pas votre groupe, cliquez sur Créer.
                    </div>

                    <div class="mt-4">
                        <select
                                name="ids"
                                id="group_id_select">
                        </select>
                    </div>


                </div>

                <div class="messages m-3"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" class="btn btn-success btn-submit">Créer</button>
                    <button type="button" class="btn btn-default btn-add-group">Ajouter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>




@pushonce('js')
    <script src="{!! asset('js/select2AjaxWrapper.js') !!}"></script>
    <script src="{!! asset('vendor/select2/js/select2.full.min.js') !!}"></script>
    <script src="{!! asset('vendor/select2/js/i18n/fr.js') !!}"></script>
@endpushonce

@pushonce('css')
    <link rel="stylesheet" href="{!! asset('vendor/select2/css/select2.min.css') !!}">
@endpushonce


@push('js')
    <script>

      $(document).ready(function() {

        const jModal = $('#modal_add_eventgroup_panel');
        const jForm = jModal.find('form');
        const jGroupSelect = jForm.find('#group_id_select');

        $('#group_id_select').select2AjaxWrapper({
          placeholder: 'Sélectionner un groupe',
          language: 'fr',
          // necessary to allow input focus inside a modal
          dropdownParent: jModal,
          ajax: {
            url: "{{ route('ajax') }}",
            delay: 100,
            dataType: 'json',
            data: function(params) {
              return {
                q: params.term,
                action: 'select2Groups',
              };
            },
          },
        });

        $('#group_id_select').on('select2:select', function(e) {
          jModal.find('.select-group-error').hide();
        });

        jForm.find('.btn-add-group').on('click', function() {
          let getEventGroupId = jGroupSelect.val();
          if (!getEventGroupId) {
            jModal.find('.select-group-error').show();
          } else {
            let formData = jForm.serialize();
            ajax('action=associateGroupsToEvent&' + formData + '&event_id={{$event->id}}', jForm, {
              successHandler: function() {
                redrawDataTable();
                return true;
              },
            });
          }

          return false;
        });

        jForm.find('.btn-submit').on('click', function() {
          window.location.href = "{!! route('panel.groups.create', [
                    'event_id' => $event->id,
                ]) !!}";
          return false;
        });
      });

    </script>
@endpush
