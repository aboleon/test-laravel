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
@push('callbacks')
    <script>
        // Debounce function to prevent multiple calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Flag to track initialization state
        let isInitializing = false;

        // Function to initialize OrderReminder UI
        function initOrderReminderUI() {
            // console.log('Initializing OrderReminder UI...');

            // Find the wrapper of the table with class .dt
            let wrapper = $('.dt').closest('.dataTables_wrapper');
            let lastCell = wrapper.find('.row:first > div:last-of-type');

            if (lastCell.length === 0) {
                lastCell = $('#order-table_wrapper .row:first > div:last-of-type');
            }

            lastCell.addClass('d-flex justify-content-end');

            // Remove any existing OrderReminder container to avoid duplicates
            $('#OrderReminder-container').remove();

            // Add the template
            lastCell.prepend($('#template-dt-order-reminder').html());
        }

        // Function to initialize OrderReminder
        function initOrderReminder() {
            // Prevent concurrent initializations
            if (isInitializing) {
                // console.log('OrderReminder initialization already in progress, skipping...');
                return;
            }

            isInitializing = true;

            // Initialize UI
            initOrderReminderUI();

            // Bind events
            OrderReminder();

            isInitializing = false;
        }

        // Create debounced version of initOrderReminder with longer delay
        const debouncedInitOrderReminder = debounce(initOrderReminder, 500);

        $(document).ready(function() {
            // Initial setup after DataTable is created
            setTimeout(function() {
                initOrderReminder();
            }, 1000);

            // Bind to DataTable events
            $('.dt').on('draw.dt', function() {
                // console.log('DataTable draw event - scheduling reinitialization');
                debouncedInitOrderReminder();
            });

            $('.dt').on('xhr.dt', function() {
                // console.log('DataTable AJAX complete - scheduling reinitialization');
                // For AJAX events, we might need extra time
                setTimeout(function() {
                    debouncedInitOrderReminder();
                }, 100);
            });
        });

        // Make functions globally available if needed
        window.initOrderReminder = initOrderReminder;
        window.debouncedInitOrderReminder = debouncedInitOrderReminder;

        function OrderReminder() {
            // console.log('Binding OrderReminder events - ' + new Date().toISOString());

            // Check if modal element exists
            let modalElement = document.getElementById('DT-OrderReminder');
            if (!modalElement) {
                console.error('Modal element DT-OrderReminder not found');
                return;
            }

            // Check if we have unpaid checkboxes - if not, try again shortly
            let unpaidCheckboxes = $('.row-checkbox.order-unpaid');
            // console.log('Unpaid checkboxes found:', unpaidCheckboxes.length);

            if (unpaidCheckboxes.length === 0 && $('.row-checkbox').length > 0) {
                // console.log('Checkboxes exist but no .order-unpaid class found - retrying in 200ms');
                setTimeout(OrderReminder, 200);
                return;
            }

            let DTModal = new bootstrap.Modal(modalElement, {
                backdrop: true
            });

            let DTSelectAll = $('#datatable-select-all'),
                DTC = $('#OrderReminder-container');

            // IMPORTANT: Always remove and rebind events to ensure they work after DataTable redraws
            // Remove any existing event handlers
            $(document).off('.orderReminder'); // Remove all namespaced events
            DTSelectAll.off('.orderReminder');
            $('#datatable-send-order-reminder').off('.orderReminder');

            // Rebind events using event delegation for checkboxes
            $(document).on('click.orderReminder', '.row-checkbox.order-unpaid', function () {
                // console.log('Unpaid checkbox clicked');
                let checkedCount = $('.row-checkbox.order-unpaid:checked').length;
                checkedCount ? DTC.removeClass('d-none') : DTC.addClass('d-none');
            });

            let OrderReminderBtn = $('#datatable-send-order-reminder');

            DTSelectAll.on('click.orderReminder', function () {
                // console.log('Select all clicked');
                let checked = $(this).is(':checked');
                $('.row-checkbox.order-unpaid').prop('checked', checked);
                checked ? DTC.removeClass('d-none') : DTC.addClass('d-none');
            });

            OrderReminderBtn.on('click.orderReminder', function () {
                let checkedUnpaid = $('.row-checkbox.order-unpaid:checked');

                if (checkedUnpaid.length > 0) {
                    DTModal.show();
                    $('#DT-OrderReminderSave').off('.orderReminder').on('click.orderReminder', function () {
                        let paramString = 'action=sendMassOrderReminder&' + $('.row-checkbox:checked').serialize();
                        $.when(ajax(paramString, $('#mfw-messages'))).then(function() {
                            DTModal.hide();
                            // Reset checkboxes after sending
                            $('.row-checkbox:checked').prop('checked', false);
                            DTC.addClass('d-none');
                        });
                    });
                } else {
                    alert('Aucune ligne n\'a été sélectionnée');
                }
            });

            // Update UI state based on current checked status
            let initialCheckedCount = $('.row-checkbox.order-unpaid:checked').length;
            initialCheckedCount ? DTC.removeClass('d-none') : DTC.addClass('d-none');

            // console.log('OrderReminder event binding complete');
        }

        // Make OrderReminder globally available
        window.OrderReminder = OrderReminder;
    </script>
@endpush
