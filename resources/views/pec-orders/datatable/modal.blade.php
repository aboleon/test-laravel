@pushonce('callbacks')
    <div class="modal fade" id="mfw-simple-modal" tabindex="-1" aria-labelledby="mfw-simple-modal_Label"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="{{ __('mfw.close') }}"></button>
                </div>
                <div id="selectable-grants-container" class="p-3">
                    <x-mfw::select name="selectable-grants" :values="[]" label="Grants disponibles"/>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal"></button>
                    <button type="button" class="btn btn-confirm"></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function DTRedraw() {
            $('.messages').not('#DT-messages .messages').html('');
            $('.dt').DataTable().ajax.reload();
            DTManipulations();
            DTclickableRow();
        }

        function fetchPecDistribution(model_id, pec_type, container) {
            ajax('action=fetchAlternativesForPecDistributionRecord&pec_distribution_id=' + model_id + '&pec_type='+ pec_type + '&callback=dispatchPecDistributionResultToModal', container)
        }

        function dispatchPecDistributionResultToModal(result) {

            let c = $('#selectable-grants-container'), options = '';
            if (!result.hasOwnProperty('error')) {
                Object.entries(result.grants).forEach(([key, value]) => {
                    options += `<option value="${key}">${value}</option>`;
                });


                c.find('select option').not(':first').remove();
                c.find('select').append(options);

            }
        }

        function DTRedraw() {
            $('.messages').not('#DT-messages .messages').html('');
            $('.dt').DataTable().ajax.reload();
            InitSimpleModal();
        }


        function InitSimpleModal() {

            setTimeout(function () {
                let mfwSimpleModal = new bootstrap.Modal(document.getElementById('mfw-simple-modal'));

                $(document).ready(function () {

                    let jQuery_mfwSimpleModal = $('#mfw-simple-modal');

                    jQuery_mfwSimpleModal.off().on('show.bs.modal', function (event) {
                        let button = $(event.relatedTarget),
                            callback = button.data('callback'),
                            onshow = button.data('onshow');

                        jQuery_mfwSimpleModal.find('.modal-title').html(button.data('title')).end()
                            .find('.modal-body').html(button.data('body')).end()
                            .find('.btn-cancel').html(button.data('btn-cancel')).end()
                            .find('.btn-confirm')
                            .addClass(button.data('btn-confirm-class'))
                            .addClass(button.data('modal-id'))
                            .attr('data-model-id', button.data('model-id'))
                            .attr('data-identifier', button.data('identifier'))
                            .html(button.data('btn-confirm'));

                        let body = jQuery_mfwSimpleModal.find('.modal-body');

                        fetchPecDistribution(button.data('model-id'), button.data('pec-type'), body);

                        $('button.modal-pec-reassign.btn-confirm').click(function () {
                            let selectedGrantId = produceNumberFromInput($('#selectable-grants').find(':selected').val());

                            if (selectedGrantId > 0) {
                                $.when(
                                    ajax('action=reassignPecDistribution&grant_id=' + selectedGrantId + '&pec_distribution_id=' + button.data('model-id'), body)).then(function () {
                                    setTimeout(function () {
                                        jQuery_mfwSimpleModal.find('.btn-cancel').trigger('click');
                                    }, 5000);
                                });
                            }
                        });


                    }).on('hide.bs.modal', function () {
                        jQuery_mfwSimpleModal.find('.modal-title, .modal-body, .btn-confirm, .btn-cancel').html('').end().find('.btn-confirm').attr('class', 'btn btn-confirm').removeAttr('data-model-id').removeAttr('data-identifier');
                        DTRedraw();
                    });
                });
            }, 1000);

        }

        InitSimpleModal();
    </script>

@endpushonce
