<template id="template-dt-order-reminder">
    <div class="me-3 d-none" id="OrderReminder-container">
        <button id="datatable-send-order-reminder"
                class="btn btn-danger btn-sm">Envoyer un e-mail de relance
        </button>
        <div class="modal fade"
             id="DT-OrderReminder"
             tabindex="-1"
             aria-labelledby="DT-OrderReminderLabel"
             aria-hidden="true">
            <form>
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DT-OrderReminderLabel">Envoi d'une relance</h5>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="{{ __('ui.close') }}"></button>
                        </div>
                        <div class="modal-body"
                             data-ajax="{{ route('ajax') }}"
                             id="DT-OrderReminderBody">
                            Envoyer un e-mail de relance pour les commandes sélectionnées
                        </div>
                        <div class="modal-footer d-flex justify-content-between"
                             id="DT-OrderReminderFooter">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                    id="DT-OrderReminderCancel">Non, annuler
                            </button>
                            <button type="button" class="btn btn-success" id="DT-OrderReminderSave">
                                <i class="fa-solid fa-enveloppe"></i> Envoyer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
</template>
@push('callbacks')
    <script>

        function OrderReminder() {
            let DTModal = new bootstrap.Modal(document.getElementById('DT-OrderReminder'), {
                backdrop: true
            });

            let DTSelectAll = $('#datatable-select-all'),
                DTC = $('#OrderReminder-container'),
                UnpaidCheckboxes = $('.row-checkbox.order-unpaid');

            UnpaidCheckboxes.off().on('click', function () {
                UnpaidCheckboxes.filter(':checked').length ? DTC.removeClass('d-none') : DTC.addClass('d-none');
            });

            let OrderReminderBtn = $('#datatable-send-order-reminder');
            OrderReminderBtn.off();

            DTSelectAll.off().on('click', function () {
                let checked = $(this).is(':checked');
                UnpaidCheckboxes.prop('checked', checked);
                checked ? DTC.removeClass('d-none') : DTC.addClass('d-none');
            });

            OrderReminderBtn.off().on('click', function () {

                UnpaidCheckboxes.filter(':checked')

                if (UnpaidCheckboxes.filter(':checked').length > 0) {
                    DTModal.show();
                    $('#DT-OrderReminderSave').off().on('click', function () {
                        let paramString = 'action=sendMassOrderReminder&' + $('.row-checkbox:checked').serialize();
                        $.when(ajax(paramString, $('#mfw-messages'))).then(DTModal.hide());
                    });
                } else {
                    alert('Aucune ligne n\'a été sélectionnée');
                }
            });
        }
    </script>
@endpush

@push('js')

    <script>

        setTimeout(function () {

            let lastCell = $('#order-table_wrapper .row:first > div:last-of-type');
            lastCell.addClass('d-flex justify-content-end');
            lastCell.prepend($('#template-dt-order-reminder').html());

            OrderReminder();


        }, 1000);
    </script>
@endpush
