if ('undefined' === typeof window.SimpleModal) {

    let jCurrentModal = null;

    window.SimpleModal = {
        create: function (config) {
            let showActionButton = config.showActionButton || false;
            let actionButtonText = config.actionButtonText || 'OK';
            let showCloseButton = config.showCloseButton || true;
            let closeButtonText = config.closeButtonText || 'Close';
            let onAction = config.onAction || function (jModal) {
            };
            let title = config.title || 'Hey!';
            let body = config.body || 'Hello, World!';

            const jModal = $('#simpleModal');
            jCurrentModal = jModal;
            jModal.find(".messages").empty();
            jModal.find('.modal-title').text(title);
            jModal.find('.modal-body').html(body);
            jModal.find('.action-button').text(actionButtonText);
            jModal.find('.close-button').text(closeButtonText);
            if (showActionButton) {
                jModal.find('.action-button').show();
            } else {
                jModal.find('.action-button').hide();
            }
            if (showCloseButton) {
                jModal.find('.close-button').show();
            } else {
                jModal.find('.close-button').hide();
            }

            jModal.find('.action-button').off('click').on('click', function () {
                onAction(jModal);
                return false;
            });

            jModal.modal('show');

        },
        setBody: function (body) {
            if (jCurrentModal) {
                jCurrentModal.find('.modal-body').html(body);
            }
        },
        hideActionButton: function () {
            jCurrentModal.find('.action-button').hide();
        },
        getSpinner: function () {
            return jCurrentModal.find('.modal-spinner');
        },
    };
}
