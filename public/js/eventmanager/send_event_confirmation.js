const sendEventContactConfirmation = {
    modalClass : '.send_event_contact_confirmation',
    modalSenderSelector : 'button.btn-confirm',
    init: function(){
        this.bindSubmit();
    },
    bindSubmit: function(){
        $(this.modalClass + ' ' + this.modalSenderSelector).off().click(function (e) {
            e.preventDefault();
            let c = $(this).closest(sendEventContactConfirmation.modalClass);
            setVeil(c.find('.modal-body'));
            ajax('action=sendEventContactConfirmationFromModal&uuid=' + c.find('input[name=uuid]').val() + '&callback=sendMailFromModalResponse&modal_id=' + c.attr('id'), c.find('.modal-body'));
        });
    }
};


$(function() {
    sendEventContactConfirmation.init();
});
