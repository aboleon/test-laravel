function bindResendOrderEmail(){
    $('.resendorderbymail button.btn-confirm').off().click(function (e) {
        e.preventDefault();
        let c = $(this).closest('.resendorderbymail');
        setVeil(c.find('.modal-body'));
        ajax('action=sendResendOrderFromModal&id=' + c.find('input[name=id]').val() + '&callback=sendMailFromModalResponse&modal_id=' + c.attr('id'), c.find('.modal-body'));

    });
}
