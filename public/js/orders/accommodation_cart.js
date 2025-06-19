// Define constants for frequently used selectors
const selectors = {
    accommodationSelector: '#accommodation-selector',
    orderAccommodationSearch: '#order-accommodation-search',
    showAccommodationRecap: '#show-accommodation-recap',
    accommodationDropdownMenuButton: '#accommodation_dropdownMenuButton',
    clientTypeSelector: '#client-type-selector :checked',
    hotelEntryDate: '#hotel_entry_date',
    hotelOutDate: '#hotel_out_date',
    eventGroupId: '#event_group_id',
    participationType: '#participation_type',
    addAccommodationRoomButton: '#add-accommodation-room-to-order button',
    accommodationCart: '#accommodation-cart',
    contingentContainer: '#contigent-container',
    accommodationTaxroom: '#accommodation-taxroom',
    orderDate: '#order_date',
    orderUUID: '#order_uuid',
};

const isGroupOrder = Boolean($(selectors.orderUUID).data('is-group-order'));
const isAmendable = $(selectors.accommodationSelector).hasClass('amendable');
const order_id = produceNumberFromInput($('#order_id').val());
const order_accommodation = {
    getAccountType: () => isAmendable ? $('#order_client_type').val() : $(selectors.clientTypeSelector).val(),
    selector: () => $(selectors.accommodationSelector),
    datepickers: () => $(`${selectors.orderAccommodationSearch} .datepicker`),
    unsetActiveSelector: function () {
        this.selector().find('li').removeClass('active');
    },
    recap: $(selectors.showAccommodationRecap),
    dropdownBtn: $(selectors.accommodationDropdownMenuButton),
    search: function (hotel_id) {
        const account_type = order_accommodation.getAccountType();
        ajax(`action=fetchAccommodationForEvent&callback=showAccommodatioRecap&event_hotel_id=${hotel_id}`
            + `&entry_date=${$(selectors.hotelEntryDate).val()}`
            + `&out_date=${$(selectors.hotelOutDate).val()}`
            + `&account_type=${account_type}`
            + `&account_id=${$('#order_' + account_type + '_id').val()}`
            + `&pec=${produceNumberFromInput($('#pec_enabled').val())}`
            + `&event_group_id=${$(selectors.eventGroupId).val()}`
            + `&participation_type=${$(selectors.participationType).val()}`,
            order_accommodation.recap);
    },
    controlDateSelector: function () {
        this.datepickers().change(function () {
            const entry_date = $(selectors.hotelEntryDate)[0]._flatpickr.selectedDates[0],
                out_date = $(selectors.hotelOutDate)[0]._flatpickr.selectedDates[0];

            order_accommodation.dropdownBtn.prop('disabled', !(entry_date instanceof Date && out_date instanceof Date && entry_date < out_date));
        });
    },
    init: function () {
        this.controlDateSelector();
        this.unsetActiveSelector();
        this.selector().find('li').click(function () {
            order_accommodation.unsetActiveSelector();
            $(this).addClass('active');
            order_accommodation.search($(this).data('id'));
            order_accommodation.dropdownBtn.text($(this).text());
        });
    },
};
setTimeout(() => order_accommodation.init(), 1000);

const accommodation_cart = {
    selector: () => $(selectors.addAccommodationRoomButton),
    cart: () => $(selectors.accommodationCart),
    contingent: () => $(selectors.contingentContainer),
    taxroomContainer: () => $(selectors.accommodationTaxroom),
    guid: () => guid(),
    getTaxRoom: function () {
        const attributes = {};
        attributes['amount'] = produceNumberFromInput(this.contingent().attr('data-taxroom'));
        attributes['net'] = produceNumberFromInput(this.contingent().attr('data-taxroom-net'));
        attributes['vat'] = produceNumberFromInput(this.contingent().attr('data-taxroom-vat'));
        attributes['vat_id'] = this.contingent().attr('data-taxroom-vatid');
        return attributes;
    },
    taxRoomRow: function (room_id) {
        return this.taxroomContainer().find('.order-taxroom-row.room-' + room_id);
    },
    addTaxRoom: function (room_id) {
        const existingTaxRow = this.taxRoomRow(room_id);
        if (existingTaxRow.length) {
            return false; // Avoid adding a redundant tax room row
        }

        const taxroom = this.getTaxRoom(),
            roomRow = $('.order-accommodation-row.accommodation-' + room_id),
            taxRow = $($('#accommodation_taxroom_template').html());

        if (taxroom['amount'] === 0) {
            return false;
        }

        const guid = this.guid(),
            pec_enabled = Boolean(produceNumberFromInput(roomRow.find('.pec_enabled').val())),
            hotelName = roomRow.find('.hotel-name').text(),
            mainText = roomRow.find('.main').text(),
            categoryText = roomRow.find('.category').text(),
            pecAllocationNet = pec_enabled ? taxroom['net'] : 0,
            pecAllocationVat = pec_enabled ? taxroom['vat'] : 0;

        taxRow.find('.hotel-name').text(hotelName).end()
            .find('.text_hotel_name').val(hotelName).end()
            .find('.main').text(mainText).end()
            .find('.text_room_label').val(mainText).end()
            .find('.category').text(categoryText).end()
            .find('.text_room_category').val(categoryText).end()
            .find('td.price').attr('data-unit-price', taxroom['amount'])
            .attr('data-unit-price-pec', 0)
            .attr('data-price-net', taxroom['net'])
            .attr('data-price-net-pec', 0)
            .attr('data-vat', taxroom['vat'])
            .attr('data-vat-pec', 0)
            .attr('data-pec-allocation-vat', pecAllocationVat)
            .attr('data-pec-allocation-net', pecAllocationNet).end()
            .find('td.price input').val(taxroom['amount']).end()
            .find('input.pec_allocation_ht').val(pecAllocationNet).end()
            .find('input.pec_allocation_vat').val(pecAllocationVat).end()
            .find('td.amount_total input').val(pec_enabled ? 0 : taxroom['amount']).end()
            .find('td.amount_net input').val(pec_enabled ? 0 : taxroom['net']).end()
            .find('td.amount_vat input').val(pec_enabled ? 0 : taxroom['vat']).end()
            .find('.vat_id').val(taxroom['vat_id']).end()
            .find('.event_hotel_id').val(order_accommodation.selector().find('.active').attr('data-id')).end()
            .find('.room_id').val(room_id).end()
            .find('.pec_enabled').val(Number(pec_enabled)).end()
            .find('.pec-mark').html(pec_enabled ? roomRow.find('.pec-mark').html() : '').end()
            .attr('data-identifier', guid);

        taxRow.find('a').attr('data-identifier', guid);
        taxRow.addClass('room-' + room_id);

        this.taxroomContainer().append(taxRow);
        this.updateTaxRowQty();
    },
    add: function () {
        this.selector().off().on('click', function () {
            accommodation_cart.contingent().find('.contingent-row').each(function () {
                const row = $(this),
                    selected = row.find(':checkbox:checked:not(:disabled)'),
                    selected_accommodation = order_accommodation.selector(),
                    active_select = selected_accommodation.find('.active');

                if (selected.length) {
                    selected.each(function () {
                        $(this).prop('disabled', true);
                        const item = $('tr.main-row.' + $(this).closest('tr').attr('data-identifier')),
                            cart_row = $($('#accommodation_cart_template').html()),
                            guid = accommodation_cart.guid(),
                            target_room = cart_row.find('.room'),
                            source_room = row.find('td.type'),
                            source_price = row.find('.sell'),
                            room_date = source_room.data('date'),
                            target_date = cart_row.find('td.date'),
                            unit_price = produceNumberFromInput($.trim(source_price.text())),
                            priceNet = source_price.data('net'),
                            priceVat = source_price.data('vat'),
                            has_date = accommodation_cart.cart().find('td.date-' + room_date),
                            pec_enabled = Boolean(produceNumberFromInput(row.find('.pec').attr('data-has-pec')) && produceNumberFromInput($('#pec_enabled').val())),
                            pec_allocation = produceNumberFromInput(row.find('.pec-allocation').attr('data-pec-allocation')),
                            pecUnitPrice = pec_enabled && pec_allocation > 0 ? unit_price - pec_allocation : 0,
                            pecNetPrice = produceNumberFromInput(source_price.data('pec-net')),
                            pecVatPrice = produceNumberFromInput(source_price.data('pec-vat')),
                            pecAllocationNet = pec_enabled ? produceNumberFromInput(source_price.data('pec-allocation-net')) : 0,
                            pecAllocationVat = pec_enabled ? produceNumberFromInput(source_price.data('pec-allocation-vat')) : 0,
                            room_id = row.find('.type :checkbox').val(),
                            availability = item.find('td.stock');


                        console.log(produceNumberFromInput($.trim(source_price.text())), 'unit_price');
                        console.log(pec_allocation, 'pec_allocation');

                        if (produceNumberFromInput(availability.attr('data-stock')) > 0) {
                            cart_row.attr('data-identifier', guid).attr('data-pec-enabled', Number(pec_enabled)).addClass('accommodation-' + room_id);
                            cart_row.find('a').attr('data-identifier', guid);

                            target_date.addClass('date-' + room_date).end().find('td.date input').val(room_date);
                            target_date.attr('data-date', room_date).attr('data-readable-date', source_room.data('readable-date'));

                            if (!has_date.length) {
                                target_date.addClass('date-visible').find('span').text(source_room.data('readable-date'));
                            }

                            target_room.find('.main').text(row.find('.type label').text());
                            target_room.find('.category').text(item.attr('data-room-group-label'));
                            target_room.find('input.room_id').val(room_id);
                            target_room.find('input.room_group_id').val(row.attr('data-room-group'));
                            target_room.attr('data-capacity', source_room.data('capacity'));
                            target_room.attr('data-room-id', source_room.data('room-id'));
                            target_room.attr('data-label', source_room.data('label'));
                            if (pec_enabled) {
                                cart_row.find('.pec-mark').html('<span class="ms-2 fw-bold"><i class="bi bi-check-circle-fill text-success"></i> PEC</span>');
                            }

                            if (unit_price === -1) {
                                target_room.find('.text-danger').text('Ce chambre n\'a pas de prix');
                            }
                            let on_quota = produceNumberFromInput(availability.attr('data-onquota'));
                            cart_row.find('td.price').attr('data-unit-price', unit_price)
                                .attr('data-unit-price-pec', pecUnitPrice)
                                .attr('data-price-net', priceNet)
                                .attr('data-price-net-pec', pecNetPrice)
                                .attr('data-vat', priceVat)
                                .attr('data-vat-pec', pecVatPrice)
                                .attr('data-pec-allocation-vat', pecAllocationVat)
                                .attr('data-pec-allocation-net', pecAllocationNet)
                                .find('input').val(unit_price);

                            cart_row.find('.vat').val(pecVatPrice).end()
                                .find('.price_ht').val(pecNetPrice).end()
                                .find('.on_quota').val(on_quota).end()
                                .find('input.qty').attr('data-onquota', on_quota);

                            cart_row.find('.pec_allocation_ht').val(pecAllocationNet).end().find('.pec_allocation_vat').val(pecAllocationVat);

                            cart_row.find('input.pec_enabled').val(Number(pec_enabled));
                            cart_row.find('td.price_total input').val(pec_enabled ? pecUnitPrice : unit_price);
                            cart_row.find('td.price_ht input').val(pec_enabled ? pecNetPrice : priceNet);
                            cart_row.find('td.vat input').val(pec_enabled ? pecVatPrice : priceVat);

                            cart_row.find('.event_hotel_id').val(active_select.attr('data-id'));
                            cart_row.find('.vat_id').val($('tr.contingent-row.main-row.' + $(this).closest('tr').attr('data-identifier')).attr('data-vat-id'));
                            cart_row.find('span.hotel').html(active_select.html());

                            if (has_date.length) {
                                cart_row.insertAfter(has_date.last().closest('tr'));
                            } else {
                                accommodation_cart.cart().append(cart_row);
                            }

                            accommodation_cart.updateStock(cart_row, 'decreaseAccommodationStock', 1);
                            accommodation_cart.manipulateRow(cart_row);
                            accommodation_cart.addTaxRoom(room_id);
                        } else {
                            const alertMsg = '<div class="alert alert-dismissible alert-danger"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>Il ne reste plus de la disponibilité ' + item.attr('data-room-group-label') + ' pour le ' + item.find('.date').text() + '</div>',
                                messagesContainer = $('#accommodation_cart_messages .messages');

                            messagesContainer.length ? messagesContainer.append(alertMsg) : $('#accommodation_cart_messages').html('<div class="messages">' + alertMsg + '</div>');
                            return false;
                        }
                    });
                    isAmendable
                        ? calculateAmendableAccommodationTotals()
                        : calculateAccommodationTotals();
                }
            });
            manageOrderBtnStatus();
        });
    },
    manipulateRow: function (row) {
        const qty = $(row).find('input.qty'),
            lockAttributions = $(row).find('.locked-attributions');

        qty.off().change(function () {
            if (Number($(this).val()) < 1) {
                $(this).val(1);
            }

            lockAttributions.addClass('d-none');

            const postvalue = produceNumberFromInput($(this).val()),
                prevalue = produceNumberFromInput(qty.attr('data-qty'));
            console.log(postvalue, 'postvalue');
            let canUpdate = true,
                stored_qty = produceNumberFromInput(qty.attr('data-stored-qty')),
                attributed = produceNumberFromInput(qty.attr('data-attributed'));

            accommodation_cart.calculateRow(row);

            if (postvalue !== prevalue) {
                const action = postvalue > prevalue ? 'decreaseAccommodationStock' : 'increaseAccommodationStock';

                if (order_id > 0) {
                    if (action === 'decreaseAccommodationStock' && postvalue <= stored_qty) {
                        canUpdate = false;
                    }
                    if (action === 'increaseAccommodationStock') {
                        if (postvalue < stored_qty) {
                            canUpdate = false;
                        }
                        if (isGroupOrder && postvalue < attributed) {
                            lockAttributions.removeClass('d-none');
                            return false;
                        }
                    }

                }

                if (canUpdate) {
                    accommodation_cart.updateStock(row, action, prevalue, $(this).attr('data-contingent-id'));
                }
            }

            qty.attr('data-qty', postvalue);
            accommodation_cart.recalculateTaxRoom(row);
        });
    },
    updateTaxRowQty: function () {
        $('.order-taxroom-row .quantity input').off().change(function () {
            const value = produceNumberFromInput($(this).val()),
                taxRow = $(this).closest('.order-taxroom-row'),
                amount = produceNumberFromInput(taxRow.attr('data-amount')),
                amount_net = produceNumberFromInput(taxRow.attr('data-amount-net')),
                amount_vat = produceNumberFromInput(taxRow.attr('data-amount-vat'));

            if (value < 1) {
                $(this).val(1);
                return false;
            }

            taxRow.find('td.amount_total input').val((amount * value).toFixed(2)).end()
                .find('td.amount_net input').val((amount_net * value).toFixed(2)).end()
                .find('td.amount_vat input').val((amount_vat * value).toFixed(2));


            isAmendable
                ? calculateAmendableAccommodationTotals()
                : calculateAccommodationTotals();
        });
    },
    recalculateTaxRoom: function (row) {
        let pecEnabled = Boolean(produceNumberFromInput($(row).attr('data-pec-enabled'))),
            room_id = produceNumberFromInput($(row).find('td.room').attr('data-room-id')),
            taxroom = this.getTaxRoom(),
            taxRow = this.taxRoomRow(room_id);

        if (!taxRow.length || pecEnabled || taxroom['amount'] === 0) return false;

        let total_room_count = 0;
        $('tr.accommodation-' + room_id).find('input.qty').each(function () {
            total_room_count += produceNumberFromInput($(this).val());
        });


        taxRow.find('td.quantity input').val(total_room_count).end()
            .find('td.amount_total input').val((taxroom['amount'] * total_room_count).toFixed(2)).end()
            .find('td.amount_net input').val((taxroom['net'] * total_room_count).toFixed(2)).end()
            .find('td.amount_vat input').val((taxroom['vat'] * total_room_count).toFixed(2));


        isAmendable
            ? calculateAmendableAccommodationTotals()
            : calculateAccommodationTotals();
    },
    calculateRow: function (row) {


        this.manipulateRow(row);

        isAmendable
            ? calculateAmendableAccommodationTotals()
            : calculateAccommodationTotals();
    },
    updateStock: function (row, action, prevalue) {
        const account_type = order_accommodation.getAccountType();

        ajax(
            'action=' + action +
            '&callback=resetAccommodationCartSelectablesStock' +
            '&date=' + row.find('td.date').attr('data-date') +
            '&event_accommodation_id=' + row.find('.event_hotel_id').val() +
            '&shoppable_model=' + this.cart().attr('data-shoppable') +
            '&shoppable_id=' + row.find('.room_group_id').val() +
            '&room_id=' + row.find('.room_id').val() +
            '&prevalue=' + prevalue +
            '&order_uuid=' + $(selectors.orderUUID).val() +
            '&quantity=' + row.find('.qty').val() +
            '&stored_quantity=' + row.find('.qty').data('stored-qty') +
            '&cart_id=' + row.data('cart-id') +
            '&account_type=' + account_type +
            '&account_id=' + $('#order_' + account_type + '_id').val() +
            '&event_group_id=' + $(selectors.eventGroupId).val() +
            '&participation_type=' + $(selectors.participationType).val() +
            '&row_id=' + row.data('identifier') +
            '&on_quota=' + row.find('.qty').attr('data-onquota') +
            '&' + row.find('input').serialize(),
            $('#accommodation_cart_messages'),
            {'keepMessages': true}
        );
    },
    amendableState: function () {
        if (isAmendable) {
            $('#accommodation-cart-original input').prop('disabled', true);
        }
    },
    init: function () {
        this.amendableState();
        this.add();
        this.updateTaxRowQty();
        this.cart().find('tr').each(function () {
            accommodation_cart.manipulateRow($(this));
        });
    },
};

function getUniqueRoomsList() {
    const uniqueRooms = [];
    const roomIdsSeen = {};

    const cart = $(selectors.accommodationCart).find('td.room');

    if (!cart.length) return false;

    cart.each(function () {
        const roomId = $(this).data('room-id');

        if (!roomIdsSeen[roomId]) {
            uniqueRooms.push({
                roomId: roomId,
                label: $(this).data('label'),
                capacity: produceNumberFromInput($(this).data('capacity'))
            });
            roomIdsSeen[roomId] = true;
        }
    });

    return uniqueRooms.length ? uniqueRooms.sort((a, b) => a.roomId - b.roomId) : [];
}

const roomsManipulator = {
    setData: function (container) {
        const row = container.find('tbody tr').last(),
            identifier = guid();
        row.attr('data-identifier', identifier);
        row.find('a').attr('data-identifier', identifier).attr('data-model');
        const select_cell = row.find('td.selectable'),
            select_label = select_cell.find('label'),
            select_tag = select_cell.find('select');
        select_label.attr('for', select_label.attr('for') + '_' + identifier);
        select_tag.attr('id', select_tag.attr('id') + '_' + identifier).attr('data-room-id', select_tag.val());
    },
    setRooms: function (container, selectables) {
        const items = container.find('td.room_selectables select');

        items.each(function (index) {
            $(this).html(selectables);
            if (produceNumberFromInput($(this).attr('data-room-id')) !== 0 && index <= items.length - 1) {
                $(this).find(`option[value=${$(this).attr('data-room-id')}]`).prop('selected', true);
            }
        });
    },
};

const accompanying = {
    container: () => $('table#accompanying'),
    template: () => $('#accompanying-template').html(),
    toggler: function () {
        $('#accompanying_toggler :checkbox').click(function () {
            if ($(this).is(':checked')) {
                accompanying.container().removeClass('d-none');
                if (!accompanying.container().find('tbody tr').length) {
                    accompanying.addRow();
                }
            } else {
                accompanying.container().addClass('d-none');
            }
        });
    },
    setSelectable: function () {
        const rooms = getUniqueRoomsList();

        if (!rooms.length) return '';
        return rooms.filter(room => room.capacity > 1)
            .map(room => `<option value="${room.roomId}" data-capacity="${room.capacity}">${room.label}</option>`)
            .join('');
    },
    setRooms: function (selectables) {
        roomsManipulator.setRooms(this.container(), selectables);
        this.assignRoom();
    },
    assignRoom: function () {
        this.container().find('td.room_acoompanying select').off().change(function () {
            accompanying.controlRoom($(this));
            $(this).attr('data-room-id', $(this).val());
        });
    },
    addRow: function () {
        const row = this.template(),
            selectables = this.setSelectable();

        $('#accompanying_errors').remove();
        if (!selectables.length) {
            $('<div id="accompanying_errors" class="mt-3 alert alert-danger simplified">Aucune chambre à capacité dépassant celle de 1 personne.</div>').insertAfter('#accompanying_toggler');
            this.container().addClass('d-none');
            $('#accompanying_toggler :checkbox').prop('checked', false);
        } else {
            this.appendRow(row);
            this.setData();
            this.setRooms(selectables);
            this.controlCapacity();
            this.controlRoom($('.accompanying_row').last().find('select'));
            rowRemover('delete_accompanying_row', 'removeAccompanyingRow', 'postAjaxremoveRowById');
        }
    },
    controlRoom: function (selectable) {
        selectable.attr('data-room-id', selectable.find('option:first').val());
        const value = produceNumberFromInput(selectable.val()),
            selected = accompanying.container().find(`select[data-room-id=${value}]`).not(selectable),
            qty = selectable.closest('tr').find('input[type=number]');

        $('.selected-accompanying-room').remove();
        qty.removeAttr('readonly');
        if (selected.length) {
            selectable.removeAttr('data-room-id');
            selectable.parent().append('<span class="selected-accompanying-room position-absolute error text-danger d-block">Cette chambre a déjà été traitée.</span>');
            qty.val('').prop('readonly', true);
        }
    },
    controlCapacity: function () {
        this.container().find('input[type=number]').off().on('change keyup paste', function () {
            const cell = $(this).parent(),
                select = $(this).closest('tr').find('select'),
                input = $(this);

            cell.find('.error').remove();

            setDelay(function () {
                const value = produceNumberFromInput(input.val()),
                    capacity = produceNumberFromInput(select.find(':selected').attr('data-capacity')) - 1;
                if (value > capacity) {
                    cell.append(`<span class="position-absolute error text-danger d-block">La capacité max est de ${capacity}</span>`);
                    input.val(capacity);
                }
            }, 500);
        });
    },
    setData: function () {
        roomsManipulator.setData(this.container());
    },
    add: function () {
        this.container().find('tfoot button').click(function () {
            accompanying.addRow();
        });
    },
    appendRow: function (row) {
        this.container().find('tbody').append(row);
    },
    init: function () {
        this.toggler();
        this.add();
        setTimeout(function () {
            accompanying.setRooms(accompanying.setSelectable());
            accompanying.controlCapacity();
            rowRemover('delete_accompanying_row', 'removeAccompanyingRow', 'postAjaxremoveRowById');
        }, 1000);
    },
};

const roomnotes = {
    container: () => $('table#roomnotes'),
    template: () => $('#roomnotes-template').html(),
    toggler: function () {
        $('#roomnotes_toggler :checkbox').click(function () {
            if ($(this).is(':checked')) {
                roomnotes.container().removeClass('d-none');
                if (!roomnotes.container().find('tbody tr').length) {
                    roomnotes.addRow();
                }
            } else {
                roomnotes.container().addClass('d-none');
            }
        });
    },
    setSelectable: function () {
        const rooms = getUniqueRoomsList();

        if (!rooms.length) return '';
        return rooms.map(room => `<option value="${room.roomId}">${room.label}</option>`).join('');
    },
    appendRow: function (row) {
        this.container().find('tbody').append(row);
    },
    addRow: function () {
        const row = this.template(),
            selectables = this.setSelectable();

        $('#roomnotes_errors').remove();
        if (!selectables.length) {
            $('<div id="roomnotes_errors" class="mt-3 alert alert-danger simplified">Aucune chambre dans la commande.</div>').insertAfter('#roomnotes_toggler');
            this.container().addClass('d-none');
            $('#roomnotes_toggler :checkbox').prop('checked', false);
        } else {
            this.appendRow(row);
            roomsManipulator.setData(this.container());
            this.setRooms(selectables);
        }
    },
    add: function () {
        this.container().find('tfoot button').click(function () {
            roomnotes.addRow();
            rowRemover('delete_roomnotes_row', 'removeRoomNotesRow', 'postAjaxremoveRowById');
        });
    },
    setRooms: function (selectables) {
        roomsManipulator.setRooms(this.container(), selectables);
    },
    init: function () {
        this.toggler();
        this.add();
        setTimeout(function () {

            rowRemover('delete_order_taxroom_row', 'removeTaxRoomRow', 'postAjaxremoveTaxRoomRow');
            roomnotes.setRooms(roomnotes.setSelectable());
        }, 1000);
    },
};

accommodation_cart.init();
accompanying.init();
roomnotes.init();
