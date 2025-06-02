<div class="row p-3" x-data="checkboxManager()">
    <div class="col-md-6 pe-sm-5 mass_checker">
        <div class="action-bar d-flex gap-3">
            <button type="button"
                    class="btn btn-info btn-sm d-flex align-items-center"
                    id="exportButton">
                <i class="bi bi-cloud-arrow-down me-2 fs-5"></i> Exporter la sélection
            </button>
            <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#new-inscription-modal"
                    class="btn btn-success btn-sm d-flex align-items-center">
                <i class="bi bi-plus-circle me-2 fs-5"></i> Nouvelle Inscription
            </button>
        </div>


        <h4 class="mt-4">Inscriptions pour les invitations</h4>
        <livewire:back.event-manager.sellable.sellable-invitation :sellable="$data"
                                                                  :event="$event" />
    </div>
</div>

@push('modals')
    @include('events.manager.sellable.modal.new-inscription-modal')
@endpush


@push("js")
    <script>


      $(document).ready(function() {
        $('#exportButton').click(function() {
          let selectedIds = [];
          $('.inscriptions-table tbody input[type="checkbox"]:checked').each(function() {
            selectedIds.push($(this).data('id'));
          });
          exportSelectedParticipants(selectedIds);
        });

        let jModal = $('#new-inscription-modal');

        jModal.find('form').on('submit', function() {
          let jForm = $(this);
          let jSpinner = jForm.find('.spinner-new-inscription');
          ajax('action=createInvitation&invitation_id={{$data->id}}&' + jForm.serialize(), jForm, {
            spinner: jSpinner,
            successHandler: function() {
              Livewire.dispatch('refreshSellableInvitations');
              return true;
            },
          });
          return false;
        });

      });

      function exportSelectedParticipants(selectedIds) {
        console.log('Selected IDs for export:', selectedIds);
        alert('Fonctionnalité en cours de développement. Merci de patienter.');
      }
    </script>
@endpush
