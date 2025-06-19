
function dispatchTransportableGrant(result) {
    removeSystemVeil();
    transportGrants.dispatchTransportableGrant(result);
}

function saveTransportableGrant(result) {
    removeSystemVeil();
    transportGrants.pushPecCallback(result);
}

function removeTransportableGrant(result) {
    removeSystemVeil();
    transportGrants.removeTransportableGrantCallback(result);
}

const transportGrants = {
    table: () => $('#transport_available_grants'),
    container: () => $('#grant-transport-container'),
    messages: () => $('#transport_grants_messages'),
    button: () => $('#transport_check_grants'),
    search: function () {
        this.button().off().click(function () {
            transportGrants.emptyMessages();
            setVeil(transportGrants.container());
            ajax('action=fetchTransportableGrant&callback=dispatchTransportableGrant&event_transport_id=' + $(this).data('id'), transportGrants.messages())
        });
    },
    emptyMessages: function () {
        this.messages().html('');
    },
    dispatchTransportableGrant: function (result) {
        if (!result.error) {
            let html = '<thead><tr><th>Grant</th><th>Type</th><th>Montant</th><th>A charge du participant</th><th></th></tr></thead>' +
                '<tbody>' +
                '<tr data-cost="' + result['grant']['transport']['cost'] + '" data-grant="' + result['grant']['transport']['grant_id'] + '">' +
                '<td>' + result['grant']['transport']['title'] + '</td>' +
                '<td>' + result['grant']['transport']['cost_type'] + '</td>' +
                '<td>' + produceNumberFromInput(result['grant']['transport']['cost']).toFixed(2) + '</td>' +
                '<td>' + produceNumberFromInput(result['grant']['transport']['surcharge']).toFixed(2) + '</td>' +
                '<td class="text-end"><button type="button" class="btn btn-sm btn-success">Affecter</button></td>' +
                '</tr>' +
                '</tbody>';

            this.table().html(html);
            this.pushPec();
        }
    },
    pushPec() {
        this.table().find('button').off().click(function () {
            let row = $(this).closest('tr');
            setVeil(transportGrants.container());
            ajax('action=saveTransportableGrant' +
                '&callback=saveTransportableGrant' +
                '&grant_id=' + row.attr('data-grant') +
                '&cost=' + row.attr('data-cost') +
                '&event_transport_id=' + transportGrants.button().data('id'), transportGrants.messages())
        });
    },
    pushPecCallback: function (result) {
        if (!result.error) {
            this.table().find('button').addClass('remove-pec btn-danger').attr('data-id', result.pec_id).text('Supprimer');
            this.table().find('.surcharge').text(result.surcharge);
            this.button().addClass('d-none');
            this.removePec();
        }
    },
    removeTransportableGrantCallback: function (result) {
        if (!result.error) {
            this.table().html('');
            this.button().removeClass('d-none');
        }
    },
    removePec() {
        this.table().find('.remove-pec').off().click(function () {
            ajax('action=removeTransportableGrant' +
                '&callback=removeTransportableGrant' +
                '&distribution_id=' + $(this).attr('data-id'), transportGrants.messages())
        });
    },
    updateAmounts: function () {
        $('.modelUpdate').keyup(function () {
            let input = $(this),
                container = input.parent();
            container.find('.suggestions').remove();
            if (!Boolean(produceNumberFromInput(input.data('id')))) {
                return false;
            }
            setDelay(function () {
                let formData = 'action=updateModelAttribute&model=' + input.data('model') +
                    '&value=' + input.val() +
                    '&id=' + input.data('id') +
                    '&column=' + input.data('column');
                ajax(formData, container);
            }, 1500);
        });
    },
    init: function () {
        this.search();
        this.removePec();
        this.updateAmounts();
    }
};

transportGrants.init();

//----------------------------------------
// autocomplete
//----------------------------------------
const jAutocompleteParticipant = $('#autocomplete-participant');
const jAutocompleteParticipantHiddenInput = $('#autocomplete-participant-hidden-input');
jAutocompleteParticipant.autocomplete({
    source: function (request, response) {
        $.ajax({
            url: jAutocompleteParticipant.data('ajax'),
        dataType: 'json',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            q: request.term,
        },
        success: function (data) {
            response($.map(data.items, function (item) {
                return {
                    label: item.text,
                    value: item.value,
                    html: item.html,
                };
            }));
        },
    });
},
minLength: 0,
    select: function (event, ui) {
    event.preventDefault();
    onParticipantSelect(ui.item.value);
},
focus: function (event, ui) {
    event.preventDefault();
    $(this).val(ui.item.label);
},
}).data("ui-autocomplete")._renderItem = function (ul, item) {
    return $("<li>")
        .append("<div>" + item.html + "</div>")
        .appendTo(ul);
};

jAutocompleteParticipant.on('keypress', function (event) {
    if (13 === event.which) {
        event.preventDefault();
        onParticipantSelect(jAutocompleteParticipant.val());
    }
});

jAutocompleteParticipant.on('focus', function () {
    $(this).autocomplete("search", "");
});


activateEventManagerLeftMenuItem('transports');


interact.selectTogglesTargets('#item_main__desired_management', {
    'divine': ['.tr-base', '.tr-divine'],
    'unnecessary': [],
    'participant': ['.tr-base', '.tr-participant'],
});

//----------------------------------------
// departure location updates return location
//----------------------------------------
$('#item_main__departure_start_location').on('input', function () {
    $('#item_main__return_end_location').val($(this).val());
});

$('#item_main__departure_end_location').on('input', function () {
    $('#item_main__return_start_location').val($(this).val());
});
