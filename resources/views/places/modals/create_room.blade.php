<script>
    function appendDymanicRoom(result) {
        if (!result.hasOwnProperty('error')) {
            let est = $('#'+result.selectable);
            est.find(':selected').prop('selected', false).change();
            est.append('<option value="' + result.room_id + '">' + result.room_name + '</option>');
            est.find('option[value=' + result.room_id + ']').prop('selected', true).change();
            setTimeout(function () {
                $('#mfwDynamicModal').modal().hide();
                $('.modal-backdrop').remove();
            }, 2000);
        }
    }
</script>

@include('places.rooms.form')

<script>
    function ajaxPostDeletePlaceRoomSetup(result) {
        $(result.input.identifier).remove();
    }
</script>
<script>
    if (typeof modal_prs === 'undefined') {
        const modal_prs = {
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
                    modal_prs.container().append($('template#place-room-setup-row').html());
                    let last_row = modal_prs.container().find('.place-room-setup-row').last(),
                        guid = modal_prs.guid();
                    last_row.attr('data-identifier', guid);
                    let deletable = last_row.find('a[data-modal-id=delete_place_room_setup]');
                    deletable.attr('data-identifier', guid).removeAttr('data-bs-toggle');
                    modal_prs.deleteSetup();
                });
            },
            deleteSetup: function () {
                modal_prs.container().find('.place-room-setup-row a').off().click(function () {
                    $(this).closest('.place-room-setup-row').remove();
                });
            },
            init: function () {
                this.add();
            },
        };
        modal_prs.init();
    }
</script>
