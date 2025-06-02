<tr class="grant-row{{ $row->id ? ' grant-'.$row->id : '' }}">
    <td>
        <x-mfw::datepicker name="date[]" :value="$row->date" :required="true"
                           :config="$dates->isNotEmpty() ? 'minDate='.$dates->first().',maxDate='.$dates->last() : ''"/>
    </td>
    <td style="min-width: 140px">
        <x-mfw::select name="room_group_id[]"
                       :affected="$row->room_group_id"
                       :values="$roomgroups"
                       class="room-group"
                       :randomize="true"
                       :nullable="false"/>
    </td>
    <td class="available text-center">
        <span>{{ $available }}</span>
    </td>
    <td class="blocked_individual">
        <x-mfw::number name="total[]"
                       min="1"
                       :params="['data-value' => $row->total]"
                       :value="$row->total"
                       :required="true"
                       class="day_stock"/>
        <input type="hidden" value="{{ $row->id }}" name="id[]"/>
    </td>
    <td class="grant_line_booked_pec text-center">0</td>
    <td class="grant_line_temp_booked_pec text-center">0</td>
    <td class="grant_line_remaning_pec text-center">0</td>
    <td class="deletable" style="width: 120px">
        <x-mfw::simple-modal id="delete_blocked"
                             class="btn btn-danger btn-sm mt-1 w-100"
                             title="Suppression d'une ligne de gestion Grant"
                             confirmclass="btn-danger"
                             confirm="Supprimer"
                             callback="ajaxDeleteGrantRow"
                             :identifier="$identifier"
                             :modelid="$row->id"
                             text='Supprimer'/>
    </td>
</tr>
@pushonce('callbacks')
    <script>
        function blockedNotice() {
            let n = $('.grant-notice');
            $('.grant-row').length < 1 ? n.removeClass('d-none') : n.addClass('d-none');
        }

        function ajaxPostDeleteGrant(result) {
            if (!result.hasOwnProperty('error')) {
                setTimeout(function () {
                    $('.' + result.input.identifier).remove();
                    blockedNotice();
                }, 500);
            }
        }

        function ajaxDeleteGrantRow() {
            $('.delete_blocked').off().on('click', function () {
                $('.messages').html('');
                let id = $(this).attr('data-model-id'),
                    identifier = $(this).attr('data-identifier'),
                    row = $('tr.grant-row.' + identifier);
                $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                if (id.length < 1 || isNaN(id)) {
                    row.remove();
                } else {
                    ajax('action=removeGrantRow&id=' + Number(id) + '&identifier=' + identifier, $('#messages'));
                }
                blockedNotice();
            });
        }
    </script>
@endpushonce

@pushonce('js')
    <script>
        /*jshint esversion: 6 */

        const grant = {
            container: function () {
                return $('#grant-container');
            },
            add: function () {
                $('#add-grant').click(function () {
                    grant.container().append($('#grant-row').html());
                    let lastrow = grant.container().find('.grant-row').last();
                    setDatepicker();
                    grant.attributeUpdater(lastrow, guid());
                    $('.grant-notice').addClass('d-none');
                    grant.controlStock();
                    // Set minimums
/*
                    $(this).find('.grant_line_booked_pec').text(stock[0].grantBooked);
                    $(this).find('.grant_line_temp_booked_pec').text(stock[0].grantTemp);
                    $(this).find('.grant_line_remaning_pec').text(stock[0].grantRemaining);

 */
                });
            },
            attributeUpdater: function (target, group_id) {
                let new_id = guid();

                target.addClass(new_id + ' ' + new_id).attr('data-row', new_id);
                target.find('.deletable a').attr('data-identifier', new_id);
            },
            parseDate: function (selected_date) {

                let date = selected_date,
                    year = date.getFullYear(),
                    month = ('0' + (date.getMonth() + 1)).slice(-2),
                    day = ('0' + date.getDate()).slice(-2);

                return `${year}-${month}-${day}`;
            },
            getStock: function (formattedDate, room_group_id, row) {

                let data = [],
                    available = 0,
                    stock_row = $('.contingent-row.date_' + formattedDate + '.stock_' + room_group_id);

                if (stock_row.length) {
                    stock_row.each(function () {
                        available += produceNumberFromInput($(this).attr('data-stock'));
                    });
                }
                let grantBlocked = produceNumberFromInput(stock_row.find('td.blocked-grant-' + formattedDate).attr('data-blocked')),
                    grantBooked = produceNumberFromInput(stock_row.find('td.grant-booked-' + formattedDate).text()),
                    grantTemp = produceNumberFromInput(stock_row.find('td.grant-temp-' + formattedDate).text()),
                    grantRemaining = produceNumberFromInput(stock_row.find('td.grant-remaining-' + formattedDate).text());

                data.push({
                    'available_raw': available,
                    'grant_blocked': grantBlocked,
                    'available':  available,
                    'grantBooked':  grantBooked,
                    'grantTemp':  grantTemp,
                    'grantRemaining':  grantRemaining,
                });

                return data;
            },
            getDateAndStock: function (row) {

                let dateStr = row.find('.datepicker').val();
                if (dateStr) {
                    let parts = dateStr.split('/');
                    let date = new Date(parts[2], parts[1] - 1, parts[0]);

                    let formattedDate = grant.parseDate(new Date(date)),
                        room_group_id = produceNumberFromInput(row.find('select.room-group').val());
                    return grant.getStock(formattedDate, room_group_id, row);
                }
                let data = [];

                data.push({
                    'available': 0
                });

                return data;
            },
            controlTypedStock: function (input) {
                let td = input.parent(),
                    row = input.closest('.grant-row'),
                    stock = grant.getDateAndStock(row),
                    stockCell = row.find('.day_stock').first(),
                    typed = produceNumberFromInput(stockCell.val()),
                    acquired = produceNumberFromInput(stockCell.attr('data-value')),
                    available = stock[0].available,
                    possible = acquired + available,
                    date = row.find('.datepicker').first().val(),
                    room_group_id = produceNumberFromInput(row.find('select').first().val());

                grant.container().find('.invalid-feedback').remove();
                grant.container().find('.is-invalid').removeClass('is-invalid');

                let errors = false;
                grant.container().find('.grant-row').not(row).each(function () {
                    let dp = $(this).find('.datepicker').first();

                    if (dp.val() === date && produceNumberFromInput($(this).find('select').first().val()) === room_group_id) {
                        errors = true;
                        row.find('.datepicker').parent().append('<div class="invalid-feedback d-block">Cette combinaison a déjà été traitée</div>');
                    }

                });

                console.log(stock);
                row.find('.available').text(available);

                if (errors === false) {

                    console.log(
                        '\nacquired + available stock is: ' + acquired,
                        '\ntyped stock is: ' + typed,
                        '\nstock is ' + available,
                        '\ntyped is greater than stock: ' + (typed > possible)
                    );

                    if (typed > possible) {
                        input.addClass('is-invalid');
                        td.append('<div class="invalid-feedback d-block">Le stock alloué n\'est pas bon.</div>');
                    } else {
                        input.removeClass('is-invalid');
                        row.find('.invalid-feedback').remove();
                    }
                }
            },
            controlStock: function () {

                // Dates
                grant.container().find('.datepicker').off().each(function () {
                    let input = $(this).closest('.grant-row').find('.day_stock').first();
                    this._flatpickr.config.onChange.push(function (
                        selectedDates, dateStr, instance) {
                        if (selectedDates[0]) {
                            grant.controlTypedStock(input);
                        }
                    });
                });

                // Stock
                grant.container().find('input[type=number], select').off().on('keyup change', function () {
                    grant.controlTypedStock($(this));
                });
            },
            save: function () {
                $('#wagaia-form').submit(function () {
                    grant.errors().addClass('d-none');
                    if (grant.container().find('.invalid-feedback').length > 0) {
                        grant.errors().removeClass('d-none');
                        return false;
                    }
                });
            },
            errors: function () {
                return $('#grant-errors');
            },
            dispatchRecorded: function () {
                grant.container().find('.grant-row').each(function () {
                    let stock = grant.getDateAndStock($(this));
                    console.log(stock, 'stock dispatchRecorded');
                    $(this).find('.available').text(stock[0].available);
                    $(this).find('.grant_line_booked_pec').text(stock[0].grantBooked);
                    $(this).find('.grant_line_temp_booked_pec').text(stock[0].grantTemp);
                    $(this).find('.grant_line_remaning_pec').text(stock[0].grantRemaining);
                });

            },
            init: function () {
                this.add();
                setTimeout(function () {
                    grant.dispatchRecorded();
                    grant.controlStock();
                }, 1000);
                this.save();
            },
        };
        grant.init();
    </script>
@endpushonce
