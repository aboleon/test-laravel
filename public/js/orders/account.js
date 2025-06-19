const payer_messages = $('#order-client-messages');

function isInvoiced() {
    return produceNumberFromInput($('#order_id').attr('data-invoiced')) === 1;
}

if (!isInvoiced()) {
    $('#client-type-selector :radio').click(function () {
        let client_type = $(this).val();
        console.log(client_type, 'client_type');
        $('#client-type-subselector > div').addClass('d-none');
        $('#order_affectable_' + client_type).removeClass('d-none');
        clearCart();
    });
    $('#payer-type-selector :radio').change(function () {
        console.log('change in #payer-type-selector');
        let client_type = $(this).val();
        console.log(client_type, 'client_type');
        $('#payer-type-subselector > div').addClass('d-none');
        $('#payable_' + client_type).removeClass('d-none').find('option:first').prop('selected', true).change();
        payer.add();
    });
}
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

function engageBookingLock(status) {
    console.log('engageBookingLock, status is ' + status);
    let statusAlert = $('.booking-lock-status');
    status === true ? statusAlert.removeClass('d-none') : statusAlert.addClass('d-none');
    $('#order-accommodation-search,#service-selector').find('input,select').prop('disabled', status);
    $('button#make-order').prop('disabled', status);
    $('button#make-order-and-redirect').prop('disabled', status);
    manageOrderBtnStatus();
}

const samepayer = {
    selector: function () {
        return $(':checkbox[name=samepayer]');
    },
    client_type: function () {
        return $('#client-type-selector :checked').val();
    },
    payerContainter: $('#payer_container'),
    ajax: function () {
        ajax('action=orderSelectAccountForAssignment&event_id=' + $('#order_uuid').attr('data-event-id') +
            '&order_client_type=' + samepayer.client_type()
            + '&order_payer_id=' + $('#order_' + samepayer.client_type() + '_id').val() +
            '&allocation_account_id=' + $('#order_contact_id').val(),
            payer_messages);
    },
    clientAccountSetup: function () {
        console.log('setting base client as payer');
        $('#order_contact_id, #order_group_id').off().select2().change(function () {
            clearCart();
            if (Number($(this).val())) {
                engageBookingLock(false);
                if (samepayer.selector().is(':checked')) {
                    samepayer.ajax();
                }
            } else {
                console.log('engageBookingLock from clientAccountSetup');
                engageBookingLock(true);
                $('#order-client-info').find('input,textarea, select').val('');
                $('#account_info').html('');
            }
        });
    },
    init: function () {
        if (!isInvoiced()) {
            this.selector().click(function () {
                if ($(this).is(':checked')) {
                    samepayer.payerContainter.find('select').each(function () {
                        $(this).find('option:first').prop('selected', true);
                    });
                    $('#payer-type-selector :radio[value=' + $('#client-type-selector :checked').val() + ']').prop('checked', true);
                    samepayer.ajax();
                    samepayer.clientAccountSetup();
                }
                $('#payer_container').toggleClass('d-none');
            });
        }

        samepayer.clientAccountSetup();
    }
}

function setAccountAsClient(result) {
    $("#selected_client_id, #selected_client_type").val('');
    if (!result.hasOwnProperty('error')) {
        let account = result.account,
            address = result.accountAddress
                ? $.trim(result.accountAddress.street_number + ' ' + result.accountAddress.route)
                : '',
            complementary_address = result.accountAddress ? result.accountAddress.prefix : '',
            text_address = result.accountAddress ? result.accountAddress.text_address : '';

        $("#selected_client_id").val(account.id);
        $("#selected_client_type").val(result.input.order_client_type);
        $('#payer_company').val(result.accountCompany == 'NC' ? '' : result.accountCompany);
        $('#payer_address_id').val(result.accountAddress ? result.accountAddress.id : '');
        $('#payer_first_name').val(account.first_name);
        $('#payer_last_name').val(result.accountName);
        $('[name="payer[text_address]"]').val(text_address);
        $('[name="payer[route]"]').val(result.accountAddress ? result.accountAddress.route : '');
        $('[name="payer[street_number]"]').val(result.accountAddress ? result.accountAddress.street_number : '');
        $('[name="payer[postal_code]"]').val(result.accountAddress ? result.accountAddress.postal_code : '');
        $('[name="payer[locality]"]').val(result.accountAddress ? result.accountAddress.locality : '');
        $('[name="payer[complementary]"]').val(result.accountAddress ? result.accountAddress.complementary : '');
        $('[name="payer[country]"]').val(result.accountAddressCountry);
        $('#payer_department').val(result.service);
        $('[name="payer[cedex]"]').val(result.accountAddress ? result.accountAddress.cedex : '');
        $('[name="payer[country_code]"]').val(result.accountAddress ? result.accountAddress.country_code : '');

        $('.gmapsbar').find('.lockable').prop('readonly', !$.trim(text_address).length > 0);

        let route = '',
            route_label = '',
            info = '';

        //Resets
        $('#pec_enabled, #participation_type').val('');
        $('#service-selector .service-item').attr('data-pec-booked', 0);

        switch (result.input.order_client_type) {
            case 'contact':
                route = '/panel/manager/event/' + result.input.event_id + '/event_contact/' + result.participationType.event_contact_id + '/edit';
                route_label = account.first_name + ' ' + account.last_name;
                info = result.participationType.group ? result.participationType.group_translated + ' / ' + result.participationType.type : ''

                let is_pec_eligible = Boolean(result.participationType?.pec_enabled && result.participationType?.pec_eligible);

                $('#participation_type').val(result.participationType?.participation_type_id ?? '');
                $('#pec_enabled').val(produceNumberFromInput(is_pec_eligible));

                // Reset PEC bookings for account
                if (result.booked_pec_services) {
                    $.each(result.booked_pec_services, function (key, value) {
                        $('#service-selector').find('.selector-service-' + key).attr('data-pec-booked', value);
                    });
                }

                $('#service-selector .price.pec_eligible').each(function () {
                    let price = $(this).attr('data-price')
                    $(this).find('span').html(is_pec_eligible ? '<del class="text-danger">' + price + ' €</del> 0' : price);
                });

                break;
            case 'group':
                route = '/panel/manager/event/' + result.input.event_id + '/event_group/' + result.event_group_id + '/edit';
                route_label = 'Groupe ' + result.accountName;
                break;
        }
        if (route) {
            $('#account_info').html('<a class="btn btn-sm btn-secondary" href="' + route + '" target="_blank">' + route_label + '</a>'
                + '<b class="ms-3">' + info + '</b>'
                + (result.participationType?.pec_enabled_by_admin ? '<span class="ms-2 fw-bold"><i class="bi bi-check-circle-fill text-' +
                    ((result.participationType?.pec_enabled && result.participationType?.pec_eligible) ? 'success' : 'danger') + '"></i> PEC</span> &nbsp;'
                    + (produceNumberFromInput(result.participationType?.pec_deposit_paid) !== 1 ? "<span class='text-danger-emphasis'> / La caution n'est pas encore réglée. Financement impossible.</span>" : '')
                    + (result.participationType?.pec_enabled_by_admin && !result.participationType?.pec_eligible ? '(n\'est plus éligible)' : '') : '')
            );

            $('#event_group_id').val(result.event_group_id ?? 0);
        }
    }
}

const payer = {
    selector: function () {
        return $('#payer_' + this.client_type() + '_id');
    },
    container: $('#order-client-info'),
    client_type: function () {
        return $('#payer-type-selector :checked').val();
    },
    add: function () {
        //console.log('selector', this.selector().length, '#payer_' + this.client_type() + '_id');
        this.selector().off().select2().change(function () {
            $('#order-client-info').find('input,textarea').val('');
            $('#account_info').html('');
            if ($(this).val()) {
                ajax('action=orderSelectAccountForAssignment&event_id=' + $('#order_uuid').attr('data-event-id') +
                    '&order_client_type=' + payer.client_type() +
                    '&order_payer_id=' + $(this).val() +
                    '&allocation_account_id=' + $('#order_contact_id').val(),
                    payer_messages
                );
            }
        });
    },
    init: function () {
        this.add();
    }
};

if (produceNumberFromInput($('#wagaia-form').attr('data-has-errors')) === 0) {

    let paramType = urlParams.has('group') ? 'group' : urlParams.has('contact') ? 'contact' : null;

    if (paramType) {
        let value = urlParams.get(paramType);
        console.log(`Value of ${paramType}:`, value);
        $(`#order_${paramType}_id option[value=${value}]`).prop('selected', true);
        $(`#client-type-selector :radio[value=${paramType}]`).trigger('click');
        $(`#payer-type-selector :radio[value=${paramType}]`).trigger('click');
        samepayer.ajax();
        engageBookingLock(false);
    }
}
samepayer.init();
payer.init();
