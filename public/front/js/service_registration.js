$(document).ready(function () {
    let jServicesContext = $('#services-context'),
        clickedEvent;

    const event_id = $('body').attr('data-event-id');

    jServicesContext.on('click', function (e) {

            let jTarget = $(e.target);
            if (jTarget.hasClass('action-add-to-cart')) {
                clickedEvent = e;
                let jTr = jTarget.closest('tr'),
                    jSpinner = jTr.closest('tr').find('.spinner-service-item'),
                    quantity = jTr.find('.select-quantity').val(),
                    serviceId = jTr.data('id');

                AjaxNotifModal.messageStyle = 'text';

                ajax({
                    action: 'frontCartAddService',
                    event_id: event_id,
                    service_id: serviceId,
                    quantity: quantity,
                }, jServicesContext, {
                    spinner: jSpinner,
                    messagePrinter: AjaxNotifModal.messagePrinter,
                    successHandler: function (data) {
                        if (data.confirm) {
                            showConfirmUpdatequantityModal(data.confirm, serviceId, quantity, 'frontCartAddService');
                        } else {
                            if (data.serviceId) {
                                onUpdateServiceQuantityAfter(data.serviceId, data.stockQuantity);
                            }
                            Livewire.dispatch('PopupCart.refresh');
                            Trail.trigger(e);
                        }
                        return true;
                    },
                });

                return false;
            }
        },
    );

    Livewire.on('PopupCart.updateServiceQuantityAfter', function (res) {
        if (res.error) {
            AjaxNotifModal.messagePrinter(200, res.ajax_messages);
        } else if (res.serviceId) {
            onUpdateServiceQuantityAfter(res.serviceId, res.stockQuantity);
            Livewire.dispatch('PopupCart.refresh');
        }
    });

    Livewire.on('PopupCart.confirmUpdateServiceQuantity', function (res) {
        let data = res[0];
        showConfirmUpdatequantityModal(data.message, data.serviceId, data.quantity, 'frontCartUpdateServiceQuantity');
    });

    function showConfirmUpdatequantityModal(text, serviceId, quantity, action) {
        SimpleConfirmModal
            .setConfirmText(text)
            .onConfirm(function () {
                ajax({
                    action: action,
                    event_id: event_id,
                    service_id: serviceId,
                    quantity: quantity,
                    force: 1,
                }, $('#simple_confirm_modal'), {
                    spinner: $('#simple-confirm-modal-spinner'),
                    messagePrinter: AjaxNotifModal.messagePrinter,
                    successHandler: function (data) {
                        if (data.serviceId) {
                            onUpdateServiceQuantityAfter(data.serviceId, data.stockQuantity);
                        }
                        Livewire.dispatch('PopupCart.refresh');
                        if (clickedEvent) {
                            Trail.trigger(clickedEvent);
                        }
                        return true;
                    },
                });

            })
            .show();
    }

    function onUpdateServiceQuantityAfter(serviceId, quantity) {
        let jServiceContainer = jServicesContext.find('.sellable-service-container[data-id="' + serviceId + '"]');
        if (jServiceContainer.length) {
            let jStockShowableContainer = jServiceContainer.find('.stock-showable-container');
            let stockShowable = jStockShowableContainer.data('stock-showable');
            if (stockShowable) {
                if (quantity <= stockShowable) {
                    jStockShowableContainer.find('.nb-remaining-stock').text(quantity);
                    jStockShowableContainer.show();
                } else {
                    jStockShowableContainer.hide();
                }
            }
        }
    }

});
