

function deletePlaceRoomSetup() {
    $('.delete_place_room_setup').off().on('click', function () {

        $('.messages').html('');
        let id = $(this).attr('data-model-id'),
            identifier = '.place-room-setup-row[data-identifier=' + $(this).attr('data-identifier') + ']';
        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
        console.log(id, identifier, (id.length < 1 || isNaN(id)));
        if (id.length < 1 || isNaN(id)) {
            $(identifier).remove();
        } else {
            ajax('action=removePlaceRoomSetup&id=' + Number(id) + '&identifier=' + identifier, $('#place_room_setup_messages'));
        }
    });
}

const prs = {
    container: function () {
        return $('#place-room-setups');
    },
    addBtn: function () {
        return $('#add-place-room-setup');
    },
    guid: function () {
        return guid();
    },
    add: function () {
        this.addBtn().off().on('click', function () {
            prs.container().append($('template#place-room-setup-row').html());
            let last_row = prs.container().find('.place-room-setup-row').last(),
                guid = prs.guid();
            last_row.attr('data-identifier', guid);
            last_row.find('a[data-modal-id=delete_place_room_setup]').attr('data-identifier', guid);
        });
    },
    init: function () {
        this.add();
    },
};
prs.init();
