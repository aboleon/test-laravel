setTimeout(function () {
    $('.sendrefundbymail button.btn-confirm').off().click(function (e) {
        e.preventDefault();
        let c = $(this).closest('.sendrefundbymail');
        setVeil(c.find('.modal-body'));
        $.when(
            ajax('action=sendRefundFromModal&uuid=' + c.find('input[name=uuid]').val() + '&callback=sendMailFromModalResponse&modal_id=' + c.attr('id'), c.find('.modal-body'))
        );

    });
}, 1000);
