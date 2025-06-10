if ('undefined' === window.redrawDataTable) {
    window.redrawDataTable = function () {
        $('.dt').DataTable().ajax.reload();
    };
}


(function ($) {
    $.fn.handleMultipleSelectionModal = function (options) {

        const defaults = {
            noSelectionMessage: 'Veuillez sélectionner au moins un élément',
            prevalidationHandler: null,
            success: function () {
                return true;
            },
            ids: [],
        };

        return this.each(function () {

            var $this = $(this);

            if ($this.data('handleMultipleSelectionModal-initialized')) {
                return;
            }

            let jModal = $(this);

            const settings = $.extend({}, defaults, options);

            jModal.off('hidden.bs.modal');
            jModal.on('hidden.bs.modal', function () {
                jModal.find('.messages').html('');
            });

            jModal.find('.submit-btn').off('click');
            jModal.find('.submit-btn').on('click', function (e) {
                e.preventDefault();
                let jForm = jModal.find('.modal-form');
                let jSpinner = jModal.find('.spinner-element');
                let jMessages = jModal.find('.messages');
                let formData = jForm.serialize();
                let ids = [...settings.ids];
                $('.row-checkbox:checked').each(function () {
                    ids.push($(this).val());
                });

                //----------------------------------------
                // pre-validation
                //----------------------------------------
                let error = null;
                let o = serializedArrayToObject(jForm.serializeArray());

                if(o.mode === 'all'){
                    let jDt = $('.dt');
                    if (jDt.length) {
                        jDt.DataTable().page.len(-1).draw();
                        jDt.find('.row-checkbox').each(function () {
                            ids.push($(this).val());
                        });
                    }
                }

                if ('selection' === o.mode && 0 === ids.length) {
                    error = settings.noSelectionMessage;
                }

                //----------------------------------------
                // process form
                //----------------------------------------
                if (error) {
                    alertDispatcher(error, jMessages, 'danger');
                } else {
                    formData += '&ids=' + ids;
                    ajax(formData, jForm, {
                        spinner: jSpinner,
                        successHandler: function () {
                            redrawDataTable();
                            return settings.success();
                        },
                    });
                }

            });

            $this.data('handleMultipleSelectionModal-initialized', true);
        });
    };
})(jQuery);
