const sendMail = {
    modalWrapper: '#mail-modal-wrapper',
    modal: '#modal_send_mail',
    modalAction: '#modal_actions_panel',
    loadPopup: function (response) {
        $(sendMail.modalWrapper).html(response.content);
        sendMail.bindModal();
        sendMail.bindAction();
        $(sendMail.modalAction).modal('hide');
        $(sendMail.modal).modal('show');
    },
    bindModal: function(){
        $(sendMail.modal).find('.submit-btn').off().on('click', function(e){
            e.preventDefault();
            let sModal = $(sendMail.modal);
            let sForm = sModal.find('.modal-form');
            let selectedAction = sModal.find('input[name="action"]:checked').val();

            if (selectedAction === 'generatePdf') {
                sForm.attr('target', '_blank');
                sForm.attr('method', 'POST');
                sForm.attr('action', '/pdf-merged');
                sForm.off('submit');
                sForm[0].submit();
                return;
            }

            ajax(sForm.serialize(), sForm);
        });
        tinymce.init(default_tinymce_settings('textarea.extended'));
    },
    bindAction: function(){
        $(sendMail.modal).find('input[name="action"]').off().on('change', function(){
           if($(this).val() === 'sendPdf'){
               $(sendMail.modal).find('.mail_object').show();
               $(sendMail.modal).find('.mail_content').show();
           } else if(($(this).val() === 'generatePdf')){
               $(sendMail.modal).find('.mail_object').hide();
               $(sendMail.modal).find('.mail_content').hide();
            } else {
               $(sendMail.modal).find('.mail_object').show();
               $(sendMail.modal).find('.mail_content').hide();
           }
        });
    }
};

function sendMailLoad(r){
    sendMail.loadPopup(r);
}
