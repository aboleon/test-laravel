setTimeout(function () {
    $('.sendinvoicebymail button.btn-confirm').off().click(function (e) {
        e.preventDefault();
        let c = $(this).closest('.sendinvoicebymail');
        setVeil(c.find('.modal-body'));
        ajax('action=sendInvoiceFromModal&uuid=' + c.find('input[name=uuid]').val() + '&callback=sendMailFromModalResponse&modal_id=' + c.attr('id'), c.find('.modal-body'));

    });
}, 1000);
