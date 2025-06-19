
function manageOrderBtnStatus() {
    console.log('managing make-order status');
    let btnMmakeOrder = $('button#make-order');
    let btnMmakeOrderAndRedirect = $('button#make-order-and-redirect');
    if (!$('#service-cart tr').length && !$('#accommodation-cart tr').length) {
        btnMmakeOrder.prop('disabled', true);
        btnMmakeOrderAndRedirect.prop('disabled', true);
    } else {
        btnMmakeOrder.prop('disabled', false);
        btnMmakeOrderAndRedirect.prop('disabled', false);
    }
}

function postMakeInvoiceFromOrder(result) {
    if (!result.hasOwnProperty('error')) {
        $('.invoiced').addClass('d-none');
        $('#service-cart, #accommodation-cart').find('input.qty').prop('readonly', true);
        $('#invoice_files').removeClass('d-none').find('.invoice-date').text(result.invoice_date);

        ajax('action=sendInvoiceFromModal&uuid=' + result.order_uuid, $('#payment-ajax-container'), {'keepMessages':true});
    }
}

function postMakeInvoiceProforma(result) {
    if (!result.hasOwnProperty('error')) {
        let proformas = $('#invoice_proforma_files'),
            proformas_table = proformas.find('table'),
            link = '/pdf/invoice/' + result.order_uuid + '?proforma=' + result.invoice_id;

        let line = '<tr style="border-top: 1px solid #ccc"><td><b>Date :</b> ' + result.invoice_date + '</td><td><a class="btn" target="_blank" href="' + link + '">Visualiser</a></td><td><a class="btn" href="' + link + '&download">Télécharger</a></td></tr>';

        if (proformas_table.find('tbody').length) {
            proformas_table.append(line);
        } else {
            proformas_table.html('<tbody>'+line+'</tbody>');
        }
    }
}

function ajaxIssueInvoice() {
    $('button.issue_invoice').off().click(function () {
        ajax('action=makeInvoice&order_id=' + $('#order_id').val() + '&callback=postMakeInvoiceFromOrder&'+ $('#invoice-texts').find('input,textarea').serialize(), $('#payment-ajax-container'), {'keepMessages':true});
        mfwSimpleModal.hide();
    });
}

function ajaxIssueInvoiceProforma() {
    $('button.issue_invoice_proforma').off().click(function () {
        ajax('action=makeInvoice&proforma&order_id=' + $('#order_id').val() + '&callback=postMakeInvoiceProforma&'+ $('#invoice-texts').find('input,textarea').serialize(), $('#payment-ajax-container'));
        mfwSimpleModal.hide();
    });
}

function clearCart()
{
    $('#account_info, #show-accommodation-recap .recap, #accommodation-cart, #service-cart').html('');
    $('#participation_type').val('');
    manageOrderBtnStatus();
    updateOrderTotals();
}

function updateGenericTotals() {
    if ($('.order-totals').length) {

        let total = 0,
            total_ht = 0,
            vat = 0;

        if ($('#service-total').length) {
            total += produceNumberFromInput($('#service-total .total').text());
            total_ht += produceNumberFromInput($('#service-total .subtotal_ht').text());
            vat += produceNumberFromInput($('#service-total .subtotal_vat').text());
        }

        if ($('#accommodation-total').length) {
            total += produceNumberFromInput($('#accommodation-total .total').text());
            total_ht += produceNumberFromInput($('#accommodation-total .subtotal_ht').text());
            vat += produceNumberFromInput($('#accommodation-total .subtotal_vat').text());
        }


        $('.order-totals').text(total.toFixed(2));
        $('.order-totals-ht').text(total_ht.toFixed(2));
        $('.order-totals-vat').text(vat.toFixed(2));
    }
}
