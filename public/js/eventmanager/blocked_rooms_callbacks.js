/*jshint esversion: 6 */

function blockedNotice() {
    let n = $('.blocked-notice');
    $('.blocked-row').length < 1 ? n.removeClass('d-none') : n.addClass('d-none');
}

function ajaxPostDeleteGroup(result) {
    if (!result.hasOwnProperty('error')) {
        setTimeout(function () {
            manageMainRowDelete(result.input.identifier);
            $('.'+result.input.identifier).remove();
            blockedNotice();
        }, 500);
    }
}

function manageMainRowDelete(identifier) {
    let row = $('tr.blocked-row.' + identifier);
    if (!row.find('.add-subline').hasClass('d-none')) {
        let subrow = row.next('[data-group=' + row.attr('data-group') + ']');
        if (subrow.length) {
            subrow.find('.add-subline').removeClass('d-none');
            subrow.find('td.participation_type > div').css('visibility', 'visible');
        }
    }
}

function ajaxDeleteBlockedRow() {
    $('.delete_blocked').off().on('click', function () {
        $('.messages').html('');
        let id = $(this).attr('data-model-id'),
            identifier = $(this).attr('data-identifier'),
            row = $('tr.blocked-row.' + identifier);
        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
        if (id.length < 1 || isNaN(id)) {
            manageMainRowDelete(identifier);
            row.remove();
        } else {
            ajax('action=removeBlockedRow&id=' + Number(id) + '&identifier=' + identifier, $('#messages'));
        }
        blockedNotice();
    });
}
