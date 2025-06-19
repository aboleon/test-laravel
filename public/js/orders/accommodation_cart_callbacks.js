function rowRemover(className, action, callback) {
    $('.' + className).off().click(function () {
        console.log('CLICKED ON ' + '.' + className);
        $('.messages').html('');
        var id = produceNumberFromInput($(this).attr('data-model-id')),
            identifier = 'tr[data-identifier=' + $(this).attr('data-identifier') + ']';
        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');

        if (id === 0) {
            $(identifier).remove();
        } else {
            ajax('action=' + action + '&callback=' + callback + '&id=' + id + '&identifier=' + identifier, $('#accommodation_cart_messages'));
        }
    });
}

function removeTaxRoomRow() {
    rowRemover('delete_order_taxroom_row', 'removeTaxRoomRow', 'postAjaxremoveTaxRoomRow');
}

rowRemover('delete_accompanying_row', 'removeAccompanyingRow', 'postAjaxremoveRowById');
rowRemover('delete_roomnotes_row', 'removeRoomNotesRow', 'postAjaxremoveRowById');

function calculateAccommodationTotals(cart) {

    //console.log('Calculating Accommodation TOTAL');
    let total = 0,
        total_ht = 0,
        total_vat = 0,
        totals = $('#accommodation-total'),
        accommodation_row = $('#accommodation-cart').find('tr');


    accommodation_row.each(function () {

        let hasPec = produceNumberFromInput($(this).attr('data-pec-enabled')) ? '-pec' : '',
            p = $(this).find('td.price'),
            unit_price = produceNumberFromInput(p.attr('data-unit-price' + hasPec)),
            net_price = produceNumberFromInput(p.attr('data-price-net' + hasPec)),
            vat = produceNumberFromInput(p.attr('data-vat' + hasPec)),
            qty = produceNumberFromInput($(this).find('input.qty').val());


        $(this).find('.price_total input').val((qty * unit_price).toFixed(2));
        $(this).find('.price_ht input').val((qty * net_price).toFixed(2));
        $(this).find('.vat input').val((qty * vat).toFixed(2));

        total += produceNumberFromInput($(this).find('.price_total input').val(), true);
        total_ht += produceNumberFromInput($(this).find('.price_ht input').val(), true);
        total_vat += produceNumberFromInput($(this).find('.vat input').val(), true);
    });

    $('#accommodation-taxroom').find('tr').each(function () {
        let hasPec = produceNumberFromInput($(this).attr('data-pec-enabled')) ? '-pec' : '',
            p = $(this).find('td.price'),
            unit_price = produceNumberFromInput(p.attr('data-unit-price' + hasPec)),
            net_price = produceNumberFromInput(p.attr('data-price-net' + hasPec)),
            vat = produceNumberFromInput(p.attr('data-vat' + hasPec)),
            qty = produceNumberFromInput($(this).find('input.qty').val());

        total += produceNumberFromInput($(this).find('.amount_total input').val(), true);
        total_ht += produceNumberFromInput($(this).find('.amount_net input').val(), true);
        total_vat += produceNumberFromInput($(this).find('.amount_vat input').val(), true);

    });

    totals.find('.total').text(total > 0 ? total.toFixed(2) : '');
    totals.find('.subtotal_ht').text(total_ht > 0 ? total_ht.toFixed(2) : '');
    totals.find('.subtotal_vat').text(total_vat > 0 ? total_vat.toFixed(2) : '');

}

// ------------------------------
// Amendable booking functions
// ------------------------------

function calculateAmendableAccommodationTotals() {
    //console.log('Calculating Accommodation TOTAL');
    let total = 0,
        total_ht = 0,
        total_vat = 0,
        totals = $('#accommodation-total-amendable');

    $('#accommodation-cart').find('tr').each(function () {
        total += produceNumberFromInput($(this).find('.price_total input').val(), true);
        total_ht += produceNumberFromInput($(this).find('.price_ht input').val(), true);
        total_vat += produceNumberFromInput($(this).find('.vat input').val(), true);
    });

    $('#accommodation-taxroom').find('tr').each(function () {
        total += produceNumberFromInput($(this).find('.amount_total input').val(), true);
        total_ht += produceNumberFromInput($(this).find('.amount_net input').val(), true);
        total_vat += produceNumberFromInput($(this).find('.amount_vat input').val(), true);
    });

    totals.find('.total').text(total > 0 ? total.toFixed(2) : '');
    totals.find('.subtotal_ht').text(total_ht > 0 ? total_ht.toFixed(2) : '');
    totals.find('.subtotal_vat').text(total_vat > 0 ? total_vat.toFixed(2) : '');

    let amendableRow = $('#accommodation-cart-original tr').first();

    $('#amendable-base-price th:first').text(function () {
        return $(this).data('title');
    });
    $('#amendable-supplement th:first').text(function () {
        return $(this).data('title');
    });
    totals.find('.' +
        'total_amended').text((produceNumberFromInput(total) - produceNumberFromInput(amendableRow.find('.price_total input').val())).toFixed(2));
    totals.find('.subtotal_ht_amended').text((produceNumberFromInput(total_ht) - produceNumberFromInput(amendableRow.find('.price_ht input').val())).toFixed(2));
    totals.find('.subtotal_vat_amended').text((produceNumberFromInput(total_vat) - produceNumberFromInput(amendableRow.find('.vat input').val())).toFixed(2));


}

// End Amendable booking functions
// ---------------------------------

function showAccommodatioRecap(result) {

    //console.log('executing showAccommodatioRecap');
    let c = $('#show-accommodation-recap'),
        recap = c.find('.recap'),
        btn = $('#add-accommodation-room-to-order');

    if (!result.hasOwnProperty('ajax_messages')) {
        c.find('.messages').html('');
    }
    recap.html('');

    btn.addClass('d-none');
    if (result.hasOwnProperty('html')) {
        recap.html(result.html);
        btn.removeClass('d-none');
        recap.find('.room-type').each(function () {
            if ($('.order-accommodation-row.' + $(this).data('target')).length) {
                $(this).find(':checkbox').prop('checked', true).prop('disabled', true);
            }
        });
    }
}

function updateOrderTotals() {

    setTimeout(function () {
        calculateAccommodationTotals();
        updateGenericTotals();
    }, 500);
}

function postAjaxremoveTaxRoomRow(result) {
    $(result.input.identifier).remove();
    updateOrderTotals();
}

function postAjaxRemoveAccommodationRow(result) {
    $(result.input.identifier).remove();
    updateOrderTotals();
}

function postAjaxremoveRowById(result) {
    $(result.input.identifier).remove();
}

function removeAccommodationRow() {

    let cart = $('#accommodation-cart');
    $('.delete_order_accommodation_row').off().on('click', function () {
        $('.messages').html('');
        let id = $(this).attr('data-model-id'),
            identifier = '.order-accommodation-row[data-identifier=' + $(this).attr('data-identifier') + ']',
            row_date = $(identifier).find('td.date'),
            room_id = $(identifier).find('.room_id').val(),
            room_group = $(identifier).find('.room_group_id').val(),
            date = row_date.attr('data-date'),
            readable_date = row_date.attr('data-readable-date');

        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
        //console.log(id, identifier, (id.length < 1 || isNaN(id)));


        if (id.length < 1 || isNaN(id)) {

            let room_line = $('#contigent-container td.room-' + room_id).closest('tr.contingent-row'),
                account_type = $('#client-type-selector :checked').val();

            ajax('action=clearAccommodationTempStock&shoppable_id=' + room_group
                + '&date=' + $(identifier).find('td.date').attr('data-date')
                + '&event_accommodation_id=' + accommodation_cart.contingent().attr('data-hotel-id')
                + '&shoppable_model=' + cart.attr('data-shoppable')
                + '&quantity=' + $(identifier).find('.qty').val()
                + '&participation_type=' + $('#participation_type').val()
                + '&date=' + $(identifier).find('td.date').attr('data-date')
                + '&room_id=' + $(identifier).find('.room_id').val()
                + '&shoppable_id=' + $(identifier).find('.room_group_id').val()
                + '&callback=resetAccommodationCartSelectablesStock'
                + '&account_type=' + account_type
                + '&account_id=' + $('#order_' + account_type + '_id').val()
                + '&order_uuid=' + $('#order_uuid').val()
                + '&before_stock=' + produceNumberFromInput(room_line.find('td.stock').attr('data-stock')), $('#accommodation_cart_messages'));

            $(identifier).remove();
            $('#contigent-container td.room-' + room_id + ' :checkbox').prop('disabled', false).prop('checked', false);
            let same_date = cart.find('td.date-' + date);
            if (same_date.length) {
                if (!same_date.find('.date-visible').length) {
                    same_date.first().addClass('date-visible').find('span').text(readable_date);
                }
            }
            calculateAccommodationTotals();
            manageOrderBtnStatus();
        } else {
            ajax('action=removeAccommodationCartRow&callback=postAjaxRemoveAccommodationRow&accommodation_cart_id=' + Number(id) + '&identifier=' + identifier, $('#accommodation_cart_messages'));
        }
    });
}

function resetAccommodationCartSelectablesStock(result) {

    if (result.hasOwnProperty('input') &&
        result.input.hasOwnProperty('shoppable_id')
    ) {
        console.log('0');

        let shoppable = $('#contigent-container').find('.stock-' + result.input.shoppable_id + '.' + result.input.date);

        if (
            (!result.hasOwnProperty('error') || (result.hasOwnProperty('error') && result.error === false))
            && result.hasOwnProperty('updated_stock')
        ) {
            console.log('1');
            shoppable.attr('data-stock', result.updated_stock).text(result.updated_stock);
            if (produceNumberFromInput(result.updated_stock) === 0) {
                shoppable.closest('tr').find(':checkbox').prop('disabled', true);
            }
        } else {
            // reset previous stock
            shoppable.attr('data-stock', result.before_stock).text(result.before_stock);
            let row = $('.order-accommodation-row[data-identifier=' + result.input.row_id + ']');
            //console.log(row, 'row from resetting stock');
            row.find('td.quantity input').val(result.input.prevalue).attr('data-qty', result.input.prevalue);
            row.find(':checkbox').prop('disabled', false);
        }
    }

    if (!result.hasOwnProperty('error') || (result.hasOwnProperty('error') && result.error === false)) {
        console.log('doing');
        updateGenericTotals();
    } else {
        console.log('abanonding');

        calculateAccommodationTotals();
    }

}

function postAjaxCancelAccommodationRow(result) {
    console.log('executing callback  postAjaxCancelAccommodationRow');
    if (!result.hasOwnProperty('error')) {

        console.log('executing callback  postAjaxCancelAccommodationRow 2');
        let row = $(result.input.identifier),
            qtyField = row.find('input.qty'),
            newQty = result.new_qty;
        row.find('.cancelled').append('<span class="text-danger">' + result.cancelled_at + ' - annulé' + result.cart_cancelled_qty + ' ch.');

        console.log(row.length, row);
        qtyField.attr('data-stored-qty', newQty).attr('data-qty', newQty).attr('data-to-attribute', result.remaining).val(newQty)

        if (result.is_group === 1 && produceNumberFromInput(result.remaining < 1)) {
            row.find('.cancel-btn').addClass('d-none');
            row.find('.remaining-attribution-count').text(newQty);
        } else {
            if (newQty < 1) {
                row.find('.cancel-btn').addClass('d-none');
            }
        }

    }
}

function appendSelectableToAccommodationCancellation(identifier) {
    let content = '',
        row = $(".order-accommodation-row[data-identifier=" + identifier + "]"),
        qtyField = row.find('.qty'),
        qty = produceNumberFromInput(qtyField.val()),
        clientType = $('input[name="order[client_type]"]:checked').val();


    content = content.concat('<div class="' + (qty < 2 ? 'd-none' : '') + '">');
    content = content.concat('<strong class="text-dark d-block pb-2">Précisez la quantité de chambres à annuler.</strong>');
    if (clientType === 'group') {
        qty = produceNumberFromInput(qtyField.attr('data-to-attribute'));
        content = content.concat('<span class="text-danger d-block pb-2">Vous pouvez annuler jusqu\'à ' + qty + ' chambres</span>');
    }
    content = content.concat('<select data-remaining="' + qty + '" class="form-control" id="accomodation_qty_cancel" name="cancel_qty">');

    for (let i = 1; i <= qty; i++) {
        content = content.concat('<option value="' + i + '">' + i + '</option>');
    }
    content = content.concat('</select></div>');


    $('#mfw-simple-modal .modal-body').html(content);
}

function cancelAccommodationRow() {

    $('.cancel_order_accommodation_row').off().on('click', function () {
        console.log('cancelAccommodationRow');
        $('.messages').html('');
        let id = Number($(this).attr('data-model-id')),
            row = '.order-accommodation-row[data-identifier=' + $(this).attr('data-identifier') + ']',
            modal = $('#mfw-simple-modal'),
            selectable = modal.find('select');


        $.when(
            ajax('action=sendOrderCartCancellationRequest&callback=postAjaxCancelAccommodationRow&service_cart_id=' + id
                + '&identifier=' + row
                + '&origin=back'
                + '&qty= ' + selectable.val()
                + '&type=' + $('#accommodation-cart').attr('data-cart-type')
                + '&remaining=' + produceNumberFromInput(selectable.attr('data-remaining'))
                + '&event_id=' + $('#order_uuid').attr('data-event-id')
                + '&cart_id=' + $(row).attr('data-cart-id')
                , $('#accommodation_cart_messages'), {'keepMessages': true}))
            .then(function () {
                modal.find('.btn-cancel').trigger('click');
            });

    });
}


function rowRemoverBindSubmit(identifier) {
    let current = $('[data-identifier="' + identifier + '"][data-bs-target="#mfw-simple-modal"]');
    let className = current.data('modal-id');
    let dataCallback = current.data('callback');

    if (typeof dataCallback === 'string') {
        dataCallback = JSON.parse(dataCallback);
    }

    let action = dataCallback[0];
    let callback = dataCallback[1];

    rowRemover(className, action, callback);
}
