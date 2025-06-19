// Callbacks
function blockedNotice() {
    let n = $('.blocked-notice');
    $('.blocked-row').length < 1 ? n.removeClass('d-none') : n.addClass('d-none');
}

function ajaxPostDeleteGroup(result) {
    if (!result.hasOwnProperty('error')) {
        setTimeout(function () {
            manageMainRowDelete(result.input.identifier);
            $('.' + result.input.identifier).remove();
            blockedNotice();
        }, 500);
    }
}

function updateAvailableCounter(result) {
    console.log(result.hasOwnProperty('updated'),'updateAvailableCounter');
    if (!result.hasOwnProperty('errors') && result.hasOwnProperty('updated')) {
        result.updated.forEach(item => {
            let cell = $('tr[data-group=' + item.group + ']').find('.available'), cell_value = produceNumberFromInput(cell.text());
            cell.text(cell_value + (item.total - cell_value));
        });
    }
}

function ajaxPostDispatchStok(result) {
    let row = $('tr.' + result.input.row_id);
    bgr.messages().html('');
    row.find('.available').text(result.availability);
}

function manageMainRowDelete(identifier) {
    let row = $('tr.blocked-row.' + identifier);
    if (!row.find('.add-subline').hasClass('d-none')) {
        let subrow = row.next('[data-group=' + row.attr('data-group') + ']');
        if (subrow.length) {
            subrow.find('.add-subline').removeClass('d-none');
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
            ajax('action=removeBlockedGroupRow&id=' + Number(id) + '&identifier=' + identifier, $('#blocked_group_room_messages'));
        }
        blockedNotice();
    });
}

// Main code

const bgr = {
    container: function () {
        return $('#blocked_group_rooms');
    },
    hotelSelector: function () {
        return this.container().find('td.hotel select');
    },
    dateSelector: function () {
        return this.container().find('td.dates select');
    },
    roomGroupSelector: function () {
        return this.container().find('td.room-groups select');
    },
    messages: function () {
        return $('#blocked_group_room_messages');
    },
    selectHotel: function () {
        this.hotelSelector().off().change(function () {
            let value = Number($(this).val()),
                cell = $(this).parent(),
                row = $(this).closest('tr');
            cell.find('.error').remove();

            console.log('CHEKING HOTEL', value);
            console.log('HAS CLASS MAIN ROW', row.hasClass('main-row'));

            if (row.hasClass('main-row')) {
                $('#blocked_group_rooms').find('.blocked-row.main-row').not(row).each(function () {
                    console.log('ALREADY SELECTED', Number($(this).find('td.hotel option:selected').val()));
                    if (Number($(this).find('td.hotel option:selected').val()) === value) {
                        cell.append('<small style="line-height: 14px" class="mt-2 d-block error text-danger">Vous avez déjà sélectionné cet hôtel.<br>Veuillez en ajouter une sous-ligne</small>');
                        return false;
                    }
                });
            }

            let hotel = $('#bgr-hotel-' + value);

            if (!hotel.length) {
                console.log('Hotel ' + value + ' not found');
                return false;
            }
            row.find('td.dates select').html(hotel.find('.dates select').html());
            row.find('td.room-groups select option').not(':first').remove();
            bgr.selectDate(hotel);
        });
    },
    selectDate: function (hotel) {
        this.dateSelector().off().change(function () {
            console.log('dateSelector changed ', '.stocks .date-' + $(this).val());
            let roomGroups = hotel.find('.stocks .date-' + $(this).val()),
                row = $(this).closest('tr'),
                options = '';
            if (roomGroups.length) {
                options = options.concat('<option value="0">--- Sélectionner ---</option>');
                roomGroups.each(function () {
                    options = options.concat('<option value="' + $(this).data('room-group') + '">' + $(this).data('name') + '</option>');
                });
            } else {
                options = options.concat('<option value="0">Aucune catégorie disponible</option>');
            }
            row.find('td.room-groups select').html(options);
        });
    },
    selectRoomGroup: function (init = false) {

        if (init) {
            return false;
        }

        this.roomGroupSelector().off().change(function () {
            console.log('changing room group');
            let value = Number($(this).val()), cell = $(this).parent(),
                error = false,
                row = $(this).closest('tr'),
                selectedDate = row.find('td.dates option:selected').val();

            cell.find('.error').remove();

            console.log('roomGroupSelector', value, row.find('.error').length);
            if (!value || row.find('.error').length) {
                console.log('stopping', value, row.find('.error').length, row.find('.error'));
                return false;
            }

            $('tr[data-group=' + row.data('group') + ']').not(row).each(function () {
                if (
                    $(this).find('td.dates option:selected').val() === selectedDate &&
                    Number($(this).find('td.room-groups option:selected').val()) === value) {
                    cell.append('<small style="line-height: 14px" class="mt-2 d-block error text-danger">Vous avez déjà dispatché du stock pour cette catégorie.</small>');
                    row.find('.blocking input').prop('disabled', true);
                    error = true;
                    return false;
                }
            });

            if (!error) {
                bgr.blockStock(row);
                let requested = produceNumberFromInput(row.find('.day_stock').val());
                row.find('.day_stock').val(0)
                bgr.getAvailability(row, requested, requested);
                row.find('.blocking input').prop('disabled', false);
            }
        });
    },
    blockStock: function (row) {

        let prevalue = produceNumberFromInput(row.find('td.blocking input').val());

        row.find('td.blocking input').off().on('keyup', function () {

            let input = $(this);

            setDelay(function () {
                bgr.getAvailability(row, prevalue, input.val());
            }, 500);
        });
    },
    add: function () {
        $('#add-blocked').click(function () {
            bgr.container().append($('#blocked-row').html());
            let lastrow = bgr.container().find('.blocked-row').last();
            lastrow.addClass('main-row');
            bgr.attributeUpdater(lastrow, guid());
            bgr.selectHotel();
            bgr.selectRoomGroup(false);
            bgr.addSubline();
            $('.blocked-notice').addClass('d-none');
            lastrow.find('.blocking input').prop('disabled', true);
            lastrow.find('.error').remove();
        });
    }
    ,
    addSubline: function () {
        $('button.add-subline').off().click(function () {
            let row = $(this).closest('.blocked-row'),
                group_id = row.attr('data-group');

            if (!row.find('td.hotel select').first().val()) {
                return false;
            }

            $($('#blocked-row').html()).insertAfter($('tr[data-group=' + group_id + ']').last()).attr('data-group', group_id);

            let subrow = $('tr[data-group=' + group_id + ']').last();
            bgr.attributeUpdater(subrow, group_id);
            let row_id = subrow.attr('data-row'),
                mainRow = $('.main-row[data-group=' + group_id + ']').first()

            bgr.selectHotel();
            bgr.selectRoomGroup(false);

            subrow.find('td.hotel').addClass('invisible ');
            subrow.find('td.hotel select option[value=' + mainRow.find('td.hotel select').val() + ']').prop('selected', true).change();
            subrow.addClass(row_id).attr('data-group', group_id).find('.key.group').val(group_id);
            subrow.find('.add-subline').addClass('d-none');
            subrow.find('.blocking input').prop('disabled', true);
            subrow.removeClass('main-row');
            subrow.find('.error').remove();

        });
    }
    ,
    attributeUpdater: function (target, group_id) {
        let new_id = guid();
        bgr.updateFormTags(target, new_id, group_id);

        target.addClass(new_id).attr('data-row', new_id).attr('data-group', group_id);
        target.find('.key.row').val(new_id).attr('name', group_id + '[row_key][]');
        target.find('.key.group').val(group_id);
        target.find('.deletable a').attr('data-identifier', new_id);
    }
    ,
    updateFormTags: function (target, new_id, group_id) {
        target.find('textarea, input, select').not('.key').each(function () {
            $(this).attr('name', group_id + '[' + new_id + '][' + $(this).attr('name') + ']');
            $(this).attr('id', $(this).attr('id') + '_' + new_id);
        });
    }
    ,
    save: function () {
        $('#save_blocked_group_rooms').click(function () {
            ajax('action=saveBlockedGroupRoomsForEvent&callback=updateAvailableCounter&event_group_id=' + bgr.container().data('event-group-id') + '&' + bgr.container().find('input, select').serialize(), bgr.messages());
        });
    }
    ,
    init: function () {
        this.selectHotel();
        this.add();
        this.addSubline();
        this.save();

        let rows = this.container().find('tr');
        if (rows.length) {
            rows.each(function () {
                let hotel_id = $(this).find('td.hotel select').val(),
                    hotel = $('#bgr-hotel-' + hotel_id),
                    dates = $(this).find('td.dates'),
                    roomGroups = $(this).find('td.room-groups');

                bgr.selectDate(hotel);
                bgr.selectRoomGroup(true);
                bgr.blockStock($(this));

                dates.find('select').html(hotel.find('.dates select').html()).end().find('option[value=' + dates.attr('data-affected') + ']').prop('selected', true).change();
                roomGroups.find('select option[value=' + roomGroups.attr('data-affected') + ']').prop('selected', true).change();

                $(this).find('.available').text(
                    $('#bgr-hotel-' + hotel_id + ' .room-group-' + roomGroups.attr('data-affected') + '.date-' + dates.attr('data-affected')).attr('data-total')
                );
            });
        }
        bgr.selectRoomGroup(false);
    },
    getAvailability(row, prevalue, requested) {
        ajax('action=getAccommodationAvailabilityForEventGroup&event_accommodation_id=' +
            row.find('td.hotel select').val() +
            '&date=' + row.find('td.dates select').val() +
            '&callback=ajaxPostDispatchStok' +
            '&roomgroup=' + row.find('td.room-groups select').val() +
            '&row_id=' + row.attr('data-row') +
            '&event_group_id=' + bgr.container().attr('data-event-group-id'),
            bgr.messages())
    }
}
bgr.init();
