function formatPrice() {
    $('input[type=number]').off().on('blur', function () {
        let input = $(this);
        setDelay(function () {
            if (input.val() < 0) {
                input.val(1);
            }
            input.val(parseFloat(input.val()).toFixed(2));
        }, 500);
    });
}

function calculateTotalPayments(result) {
    console.log('calculateTotalPayments')
    let sum = 0,
        cells = $('#payment-rows tr:visible td.amount'),
        toPay = getInvoiceableAmountToPay();


    cells.each(function () {
        let value = Number($(this).attr('data-value'));
        if (!isNaN(value)) {
            sum += value;
        }
    });

    $('#payments-paid').attr('data-amount', sum).text(sum.toFixed(2) + ' €');
    $('#payments-remaining').attr('data-amount', toPay - sum).text((toPay - sum).toFixed(2) + ' €');

    manageInvoiceableStatus(result);

}


let jAjaxContainer = null;

function deleteOrderPayment(e, jTrigger) {
    let id = jTrigger.closest('tr').data('id');
    ajax('action=deleteOrderPayment&toPay=' + getInvoiceableAmountToPay +
        '&id=' + id, jAjaxContainer, {
            successHandler: function (response) {
                jTrigger.closest('tr').remove();
                calculateTotalPayments(response);
                return true;
            },
        }
    )
    ;
}

$(document).ready(function () {

    jAjaxContainer = $('#payment-ajax-container');
    const jPaymentContainer = $('#payment-container');
    const jPaymentAddRowBtn = jPaymentContainer.find('.btn-add-row');
    const jPaymentTableBody = jPaymentContainer.find('.table-payment tbody');
    const jPaymentEditTemplate = $('#payment-edit-row-template');
    const jPaymentTemplate = $('#payment-row-template');

    //----------------------------------------
    // add new row
    //----------------------------------------
    jPaymentAddRowBtn.on('click', function (e) {
        e.preventDefault();

        const templateContent = $(jPaymentEditTemplate.prop('content')).clone();
        jPaymentTableBody.append(templateContent);
        formatPrice();

        refreshDynamicFeatures();
    });

    //----------------------------------------
    // save row
    //----------------------------------------
    jPaymentTableBody.on('click', '.btn-validate', function (e) {
        e.preventDefault();
        let jRow = $(this).closest('tr');
        let data = {};

        jRow.find('input:not([readonly]), select').each(function () {
            let name = $(this).attr('name');
            let value = $(this).val();
            name = name.replace('payment[', '').replace(']', '').replace('[]', '');
            data[name] = value;
        });
        data.order_id = orderId;
        data.topay = produceNumberFromInput($('#payments-total').attr('data-amount'));
        data.remainingAmount = produceNumberFromInput($('#payments-remaining').attr('data-amount'));
        let formData = $.param(data);
        ajax('action=saveOrderPayment&' + formData, jAjaxContainer, {
            successHandler: function (response) {
                data.id = response.id;
                addRow(data);
                jRow.remove();
                calculateTotalPayments(response);
                return true;
            },
        });
    });

    //----------------------------------------
    // edit row
    //----------------------------------------
    jPaymentTableBody.on('click', '.btn-edit', function (e) {
        e.preventDefault();

        let jRow = $(this).closest('tr');
        let rowId = jRow.data('id');
        jRow.hide();

        const templateContent = $(jPaymentEditTemplate.prop('content')).clone();

        $(templateContent).find('[name="payment[id][]"]').val(rowId);
        $(templateContent).find('[name="payment[date][]"]').val(jRow.find('.date').data('value'));
        $(templateContent).find('[name="payment[amount][]"]').val(jRow.find('.amount').data('value'));
        $(templateContent).find('[name="payment[payment_method][]"]').val(jRow.find('.payment_method').data('value'));
        $(templateContent).find('[name="payment[authorization_number][]"]').val(jRow.find('.authorization_number').data('value'));
        $(templateContent).find('[name="payment[card_number][]"]').val(jRow.find('.card_number').data('value'));
        $(templateContent).find('[name="payment[bank][]"]').val(jRow.find('.bank').data('value'));
        $(templateContent).find('[name="payment[issuer][]"]').val(jRow.find('.issuer').data('value'));
        $(templateContent).find('[name="payment[issuer][]"]').val(jRow.find('.issuer').data('value'));
        $(templateContent).find('[name="payment[check][]"]').val(jRow.find('.check').data('value'));

        $(templateContent).find('tr').data('related-row', rowId);
        $(templateContent).find('.btn-delete').hide();
        $(templateContent).find('.btn-cancel').show();
        jRow.after(templateContent);
        refreshDynamicFeatures();
    });

    //----------------------------------------
    // cancel edit
    //----------------------------------------
    jPaymentTableBody.on('click', '.btn-cancel', function (e) {
        e.preventDefault();

        cleanTooltips();
        let jTemplateRow = $(this).closest('tr');
        let rowId = jTemplateRow.data('related-row');
        let jOriginalRow = jPaymentTableBody.find('[data-id="' + rowId + '"]');
        jOriginalRow.show();
        jTemplateRow.remove();

    });

    //----------------------------------------
    // delete dynamically created rows
    //----------------------------------------
    jPaymentTableBody.on('click', '.btn-delete', function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        cleanTooltips();
    });

    function addRow(payment) {
        let templateHtml = jPaymentTemplate.html(),
            date_formatted = formatDate(payment.date),
            transaction_id = payment.front_transaction?.transaction_id ?? (payment.payment_transaction?.transaction_id ?? ''),
            is_paybox = payment.payment_method === 'cb_paybox';

        let row = templateHtml
            .replace(/{id}/g, payment.id)
            .replace(/{date}/g, payment.date)
            .replace(/{date_display}/g, date_formatted)
            .replace(/{amount}/g, Number(payment.amount).toFixed(2))
            .replace(/{payment_method}/g, payment.payment_method)
            .replace(/{payment_method_display}/g, paymentMethods[payment.payment_method])
            .replace(/{authorization_number}/g, transaction_id)
            .replace(/{card_number}/g, payment.card_number ?? '')
            .replace(/{bank}/g, payment.bank ?? '')
            .replace(/{issuer}/g, payment.issuer ?? '')
            .replace(/{check_number}/g, payment.check_number ?? '')
            .replace(/{is_cb_paybox}/g, is_paybox ? '' : 'invoiced d-none');

        let $row = $($.parseHTML(row)), refundLink = $row.find('[data-modal-id="reimburse-modal"]');

        if (is_paybox && payment.reimbursed_at !== null) {
            $row.find('.payment_method').append('<span class="ms-1 badge bg-danger">Remboursé</span>');

            if (payment.refund !== null) {
                $row.find('.actions').html(
                    '<a href="/panel/manager/event/' + $('#order_uuid').data('event-id') + '/refunds/' + payment.refund.id +
                    '/edit" class="mfw-edit-link btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Éditer l\'avoir">\n' +
                    '                <i class="fas fa-pen"></i></a>' +
                    '<a href="/pdf/refundable/' + payment.refund.uuid + '" class="mfw-edit-link btn btn-sm btn-danger" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PDF Avoir"><i class="fas fa-file-pdf"></i></a>'
                );
            }
        } else {
            refundLink.attr('data-model-id', payment.id);
        }
        jPaymentTableBody.append($row);

        let addedRow = jPaymentTableBody[0].lastElementChild;
        let tooltipTriggerList = addedRow.querySelectorAll('[data-bs-toggle="tooltip"]');
        Array.from(tooltipTriggerList).map(tooltipTriggerEl =>
            new bootstrap.Tooltip(tooltipTriggerEl)
        );


    }

    function refreshDynamicFeatures() {
        setDatepicker();
        formatPrice();
        setTimeout(function () {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }, 300);
    }

    function cleanTooltips() {
        $('.tooltip').remove();
    }

    //----------------------------------------
    // initial rows
    //----------------------------------------
    paymentsData.forEach(function (payment) {
        addRow(payment);
    });

    $('#payment-rows td.amount').each(function () {
        let amount = Number($(this).text()).toFixed(2);
        $(this).text(amount).attr('data-value', amount);
    });


});
