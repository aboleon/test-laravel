const sendEventGroupConfirmation = {
    modalClass : '.send_event_group_confirmation',
    modalSenderSelector : 'button.btn-confirm',
    init: function(){
        this.bindSubmit();
    },
    bindSubmit: function(){
        $(this.modalClass + ' ' + this.modalSenderSelector).off().click(function (e) {
            e.preventDefault();
            let c = $(this).closest(sendEventGroupConfirmation.modalClass);
            setVeil(c.find('.modal-body'));
            ajax('action=sendEventGroupConfirmationFromModal&uuid=' + c.find('input[name=uuid]').val() + '&callback=sendMailFromModalResponse&modal_id=' + c.attr('id'), c.find('.modal-body'));
        });
    }
};

function uncheckContact()
{
    $('input[name="contacts[]"]:checked').prop('checked', false);
}
$(function() {
    sendEventGroupConfirmation.init();
});
