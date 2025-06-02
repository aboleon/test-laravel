<div class="modal fade"
     id="generate_event_group_for_contact"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog">
        <form method="GET" action="{{route('pdf-printer', ['type' => 'eventGroupConfirmation', 'identifier' => encrypt($eventGroup->id)])}}">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex align-items-end">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Selectionnez les participants qui doivent figurer dans le PDF</h1>
                    <div class="spinner-element ms-3" style="display: none;">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">
                    @include('events.manager.event_group.modal.group-contact-row')
                </div>

                <div class="messages m-3"></div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-warning">Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('js')
    <script>
        $(document).ready(function() {
            $("#generate_event_group_for_contact form").submit(function(event) {
                event.preventDefault();
                $(this).find('.messages').html('');
                console.log($(this));
                let selectedContacts = [];
                $(this).find('input[name="contacts[]"]:checked').each(function() {
                    selectedContacts.push($(this).val());
                });

                if(!selectedContacts.length){
                    $(this).find('.messages').html('<div class="alert alert-error">Veuillez selectionner des contacts</div>');
                }

                let queryString = selectedContacts.length ? "?contacts=" + selectedContacts.join(",") : "";
                window.location.href = $(this).attr("action") + queryString;
            });
        });
    </script>
@endpush
