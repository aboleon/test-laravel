function calculateServiceRow(row) {

    console.log('calculateServiceRow');
    /*
        function calculateHelper(row, qty, pec_maxed, pec_justmaxed) {
           // console.log('calculateHelper');

            let unitPrice = produceNumberFromInput(p.attr('data-unit-price')),
                netPrice = produceNumberFromInput(p.attr('data-price-net')),
                vatPrice = produceNumberFromInput(p.attr('data-vat'));

            console.log(
                '\nqty ' + produceNumberFromInput($(row).find('input.qty').val()),
                '\nqty_evaluated ' + qty,
                '\npec_max ' + pec_max,
                '\npec_maxed ' + pec_maxed,
                '\npec_justmaxed ' + pec_justmaxed,
                '\nunitPrice ' + unitPrice,
                '\nnetPrice ' + netPrice,
                '\nvatPrice ' + vatPrice,
            );

            $(row).find('.price_total input').val((qty * unitPrice).toFixed(2));
            $(row).find('.price_ht input').val((qty * netPrice).toFixed(2));
            $(row).find('.vat input').val((qty * vatPrice).toFixed(2));

            calculateServiceCartTotals($('#service-cart'), $('#service-total'));
        }
        */


    let p = $(row).find('td.price'),
        service_id = produceNumberFromInput($(row).find('.service_id').val()),
        service = $('#service-selector').find('.selector-service-' + service_id),
        pec_enabled = Boolean(produceNumberFromInput(service.attr('data-pec-enabled')) && produceNumberFromInput($('#pec_enabled').val())),
        pec_max = produceNumberFromInput(service.attr('data-pec-max')),
        pec_maxed = Boolean(produceNumberFromInput($(row).find('td.quantity').attr('data-pec-maxed'))),
        pec_justmaxed = $(row).find('.pec-just-maxed').length,
        pec_booked = produceNumberFromInput($(row).find('td.quantity').attr('data-pec-booked')),
        pec_copies = row.find('.pec-copies').length,
        qty = produceNumberFromInput($(row).find('input.qty').val());


    let unitPrice = produceNumberFromInput(p.attr('data-unit-price')),
        netPrice = produceNumberFromInput(p.attr('data-price-net')),
        vatPrice = produceNumberFromInput(p.attr('data-vat'));

    if (pec_enabled) {

        if (!pec_maxed) {

            qty = qty - pec_booked;

            if (qty > pec_max) {

                qty = qty - pec_max;
/*
                if (pec_copies) {
                    calculateHelper(row, qty, true, true);
                    return false;
                }

 */
                if (!pec_justmaxed && !row.find('.pec-maxed').length) {
                    // console.log('Max PEC atteinte');
                    row.find('.pec_label').append('<span class="d-block pec-just-maxed text-danger">Max PEC atteinte</span>');
                }

            } else {
                qty = 0;

                if (!row.find('.pec-maxed').length) {
                    row.find('.pec-just-maxed').remove();
                }

            }

        }
    }


    $(row).find('.price_total input').val((qty * unitPrice).toFixed(2));
    $(row).find('.price_ht input').val((qty * netPrice).toFixed(2));
    $(row).find('.vat input').val((qty * vatPrice).toFixed(2));

    calculateServiceCartTotals($('#service-cart'), $('#service-total'));
}

function calculateServiceCartTotals(cart, totals) {
    let total = 0,
        total_ht = 0,
        total_vat = 0;

    cart.find('tr').each(function () {

        total += produceNumberFromInput($(this).find('td.price_total input').first().val());
        total_ht += produceNumberFromInput($(this).find('td.price_ht input').first().val());
        total_vat += produceNumberFromInput($(this).find('td.vat input').first().val());
    });

    totals.find('.total').text(total > 0 ? total.toFixed(2) : 0);
    totals.find('.subtotal_ht').text(total_ht > 0 ? total_ht.toFixed(2) : 0);
    totals.find('.subtotal_vat').text(total_vat > 0 ? total_vat.toFixed(2) : 0);

    updateGenericTotals();
}


function postAjaxRemoveServiceRow(result) {
    if (!result.hasOwnProperty('error') || result?.error === false) {
        $(result.input.identifier).remove();

        service_cart.calculateTotal();

        if (result.hasOwnProperty('putBackInStock')) {
            let item = $('#service-selector').find('.service-item[data-id=' + result.input.shoppable_id + ']').first();

            if (item) {
                let restock = Number(result.putBackInStock) ?? 0;
                item.attr('data-stock', restock).find('.stock-remaining').text(restock);
                if (Number(item.attr('data-stock')) > 0) {
                    item.find(':checkbox').prop('disabled', false).prop('checked', false);
                }
            }
        }
        manageOrderBtnStatus();
    }
}

function postAjaxCancelServiceRow(result) {
    if (!result.hasOwnProperty('error')) {
        let jTr = $(result.input.identifier);
        jTr.find('.cancel-btn').addClass('d-none');
        jTr.find('.cancelled').removeClass('d-none').find(".cancelled_time").text(result.cancelled_at);

        service_cart.calculateTotal();

        if (result.hasOwnProperty('putBackInStock')) {
            let item = $('#service-selector').find('.service-item[data-id=' + result.input.shoppable_id + ']').first();

            if (item) {
                let restock = Number(item.attr('data-stock') ?? 0) + (Number(result.putBackInStock) ?? 0);
                item.attr('data-stock', restock).find('.stock-remaining').text(restock);
                if (Number(item.attr('data-stock')) > 0) {
                    item.find(':checkbox').prop('disabled', false).prop('checked', false);
                }
            }
        }
        manageOrderBtnStatus();
    }
}

function removeServiceRow() {

    $('.delete_order_service_row').off().on('click', function () {
        console.log('removeServiceRow');
        $('.messages').html('');
        let id = Number($(this).attr('data-model-id')),
            uuid = $(this).attr('data-identifier'),
            identifier = '.order-service-row[data-identifier=' + uuid + ']';
        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
        // console.log(id, identifier, (id.length < 1 || isNaN(id)));

        if ($(identifier).hasClass('unlimited_stock')) {
            $(identifier).remove();
            service_cart.calculateTotal();
            service_cart.selector().find(':checked').prop('checked', false);
        } else {

            let account_type = $('#client-type-selector :checked').val();

            ajax('action=removeServicePriceRow&callback=postAjaxRemoveServiceRow&service_cart_id=' + id +
                '&identifier=' + identifier +
                '&uuid=' + uuid +
                '&order_uuid=' + $('#order_uuid').val() +
                '&account_type=' + account_type +
                '&account_id=' + $('#order_' + account_type + '_id').val() +
                '&shoppable_id=' + $(identifier)?.find('.service_id').val() +
                '&shoppable_model=' + $('#service-cart').data('shoppable'),
                $('#service_cart_messages'), {'keepMessages': true}
            );
        }
    });
}

function cancelServiceRow() {

    $('.cancel_order_service_row').off().on('click', function () {
        console.log('cancelServiceRow');
        $('.messages').html('');
        let id = Number($(this).attr('data-model-id')),
            identifier = '.order-service-row[data-identifier=' + $(this).attr('data-identifier') + ']';
        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
        console.log(id, identifier, (id.length < 1 || isNaN(id)));

        if ($(identifier).hasClass('unlimited_stock')) {
            $(identifier).remove();
            service_cart.calculateTotal();
            service_cart.selector().find(':checked').prop('checked', false);
        } else {

            let account_type = $('#client-type-selector :checked').val();

            ajax('action=cancelServicePriceRow&callback=postAjaxCancelServiceRow&service_cart_id=' + id
                + '&identifier=' + identifier
                + '&account_type=' + account_type
                + '&account_id=' + $('#order_' + account_type + '_id').val()
                + '&order_uuid=' + $('#order_uuid').val()
                + '&shoppable_id=' + $(identifier)?.find('.service_id').val()
                + '&shoppable_model=' + $('#service-cart').data('shoppable')
                , $('#service_cart_messages'), {'keepMessages': true});
        }
    });
}

function resetServiceCartSelectablesStock(result) {

    if (result.hasOwnProperty('input') &&
        result.input.hasOwnProperty('shoppable_id')
    ) {

        let shoppable = $('#service-selector').find('.service-item[data-id=' + result.input.shoppable_id + ']').first();

        if (
            (!result.hasOwnProperty('error') || (result.hasOwnProperty('error') && result.error === false))
        ) {
            if (result.hasOwnProperty('updated_stock')) {
                shoppable.attr('data-stock', result.updated_stock).find('.stock-remaining').text(result.updated_stock);
            }
        } else {
            console.log("resetting stock");
            let row = $('.order-service-row[data-identifier=' + result.input.row_id + ']');
            console.log(row, 'row from resetting stock');
            row.find('td.quantity input').val(result.input.prevalue).attr('data-qty', result.input.prevalue);
            calculateServiceRow(row);
        }

    }

    calculateServiceCartTotals($('#service-cart'), $('#service-total'));

}
