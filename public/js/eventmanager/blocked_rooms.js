/*jshint esversion: 6 */


const blocked = {
    container: function () {
        return $('#blocked-container');
    },
    errors: function () {
        return $('#blocked-errors');
    },
    messages: function () {
        return $('#blocked-messages');
    },
    add: function () {
        $('#add-blocked').click(function () {
            blocked.container().append($('#blocked-row').html());
            let lastrow = blocked.container().find('.blocked-row').last();
            setDatepicker();
            blocked.attributeUpdater(lastrow, guid());
            blocked.addSubline();
            blocked.controlStock();
            blocked.disableUsedParticipationTypes(lastrow);
            blocked.setParticipationTypeAttrubute(lastrow);
            $('.blocked-notice').addClass('d-none');
        });
    },
    attributeUpdater: function (target, group_id) {
        let new_id = guid();
        blocked.updateFormTags(target, new_id, group_id);

        target.addClass(new_id).attr('data-row', new_id).attr('data-group', group_id);
        target.find('.key.row').val(new_id).attr('name', group_id + '[row_key][]');
        target.find('.key.group').val(group_id);
        target.find('.deletable a').attr('data-identifier', new_id);
    },
    updateFormTags: function (target, new_id, group_id) {
        target.find('textarea, input, select').not('.key, :checkbox').each(function () {
            $(this).attr('name', group_id + '[' + new_id + '][' + $(this).attr('name') + ']');
            $(this).attr('id', $(this).attr('id') + '_' + new_id);
        });
        target.find(':checkbox').each(function () {
            let n = $(this).attr('name');
            $(this).attr('name', group_id + '[' + new_id + '][' + n.replace('[]', '') + '][]');
            $(this).attr('id', $(this).attr('id') + '_' + new_id);
        });
    },
    addSubline: function () {
        $('button.add-subline').off().click(function () {
            let row = $(this).closest('.blocked-row'),
                group_id = blocked.getRowGroup(row);

            $($('#blocked-row').html()).insertAfter($('tr[data-group=' + group_id + ']').last()).attr('data-group', group_id);

            let subrow = $('tr[data-group=' + group_id + ']').last();
            blocked.attributeUpdater(subrow, group_id);
            setDatepicker();
            let row_id = subrow.attr('data-row');

            subrow.removeClass('main-row').addClass('subrow');
            subrow.addClass(row_id).attr('data-group', group_id).find('.key.group').val(group_id);
            subrow.find('td.participation_type > div').addClass('d-none');
            subrow.find('td.grant').css('visibility', (row.find('td.grant').css('visibility') === 'hidden' ? 'hidden' : 'visible'));
            subrow.find('.add-subline').addClass('d-none');

            let ptypes = row.find(':checked').map(function () {
                return this.value;
            }).get();

            subrow.find('.participation_type :checkbox').each(function() {
                if (ptypes.includes(this.value)) {
                    $(this).prop('checked', true);
                }
            });

            subrow.find('td.participation_type').attr('data-recorded', blocked.participationTypes(subrow));
            blocked.controlStock();
            blocked.setParticipationTypeAttrubute(subrow);
        });
    },
    parseDate: function (selected_date) {

        let date = selected_date,
            year = date.getFullYear(),
            month = ('0' + (date.getMonth() + 1)).slice(-2),
            day = ('0' + date.getDate()).slice(-2);

        return `${year}-${month}-${day}`;
    },
    setParticipationTypeAttrubute: function (row) {
        let c = row.find('.participation_type'),
            boxes = c.find(':checkbox');

        boxes.off().click(function () {
            c.attr('data-recorded', blocked.participationTypes(row));
            blocked.controlTypedStock(row);
        });
    },
    disableUsedParticipationTypes: function(row) {

        let ptypes = Array.from(new Set($('.blocked-row.main-row').not(row).find(':checked').map(function () {
            return this.value;
        }).get()));

        if (ptypes.length) {
            row.find('.participation_type :checkbox').each(function() {
                if (ptypes.includes(this.value)) {
                    $(this).prop('disabled', true);
                }
            });
        }

    },
    participationTypes: function (element) {
        return element.find('.participation_type').find(':checked').map(function () {
            return this.value;
        }).get().join(',')
    },
    getRowGroup: function(row) {
        return row.attr('data-group');
    },
    controlTypedStock: function (row) {

        setDelay(function () {

            let input = row.find('.day_stock'),
                date = row.find('.datepicker').first().val(),
                room_group = row.find('select.room-group').first().val(),
                rowgroup = blocked.getRowGroup(row);


            blocked.container().find('.invalid-feedback').remove();
            blocked.container().find('.is-invalid').removeClass('is-invalid');

            let errors = false;
            blocked.container().find('.blocked-row[data-group=' + rowgroup + ']').not(row).off().each(function () {

                let dp = $(this).find('.datepicker').first(),
                    rg = $(this).find('select.room-group').first();

                if ((dp.val() === date && rg.val() === room_group)) {
                    errors = true;
                    if (dp.val() === date) {
                        row.find('.datepicker').parent().append('<div class="invalid-feedback d-block">Cette date a déjà été traitée</div>');
                    }
                    if (rg.val() === room_group) {
                        row.find('select.room-group').parent().append('<div class="invalid-feedback d-block">Cette catégorie a déjà été traitée</div>');
                    }
                }

            });
        }, 500);
    },
    save: function () {
        $('#wagaia-form').submit(function () {
            blocked.errors().addClass('d-none');
            if (blocked.container().find('.invalid-feedback').length > 0) {
                blocked.errors().removeClass('d-none');
                return false;
            }
        });
    },
    controlStock: function () {

        // Room categories
        $('#blocked-container select.room-group').off().change(function () {
            blocked.controlTypedStock($(this).closest('.blocked-row'));
        });

        // Dates
        $('#blocked-container .datepicker').off().each(function () {
            let row = $(this).closest('.blocked-row');
            this._flatpickr.config.onChange.push(function (
                selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    blocked.controlTypedStock(row);
                }
            });
        });

        // Stock
        $('#blocked-container input.day_stock').off().on('keyup', function () {
            blocked.controlTypedStock($(this).closest('.blocked-row'));
            blocked.controlGrant($(this).closest('tr').find('td.grant input[type=number]'));
        });

        // Stock
        blocked.container().find('td.grant input[type=number]').off().on('keyup change', function () {
            blocked.controlGrant($(this));
        });
    },
    controlGrant: function (input) {
        let td = input.parent();
        td.find('.invalid-feedback').remove();
        input.removeClass('is-invalid');
        if (Number(input.val()) > Number(input.closest('tr').find('.day_stock').first().val())) {
            input.addClass('is-invalid');
            td.append('<div class="invalid-feedback d-block">Le stock alloué n\'est pas bon.</div>');
        } else {
            input.removeClass('is-invalid');
            td.find('.invalid-feedback').remove();
        }
    },
    init: function () {
        this.add();
        this.addSubline();
        setTimeout(function () {
            $('.blocked-row.main-row').each(function() {
                blocked.disableUsedParticipationTypes($(this));
            });
            blocked.controlStock();
        }, 1000);
        this.save();
    },
};
blocked.init();
