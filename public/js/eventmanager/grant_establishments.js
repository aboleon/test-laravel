function deleteGrantEstablishment() {
    $('.delete_grant_binded_establishment').off().on('click', function () {

        $('.grant_binded_messages').html('');
        let id = $(this).attr('data-model-id'),
            identifier = '#grant-establishments-validated tr[data-identifier=' + $(this).attr('data-identifier') + ']';

        $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
        console.log(id, identifier, (id.length < 1 || isNaN(id)));
        if (id.length < 1 || isNaN(id)) {
            $('#grant-establishments tr.' + $(identifier).attr('class')).removeClass('d-none');
            $(identifier).remove();
        }
    });
}

const es = {
    eligbleAlert: function () {
        return $('.eligible-establishments-alert');
    },
    country_selector: function () {
        return $('#grant_country_selector');
    },
    locality_selector: function () {
        return $('#grant_locality_selector');
    },
    messages: function () {
        return $('#grant-establishment-messages');
    },
    country_select: function () {
        this.country_selector().change(function () {
            es.eligbleAlert().addClass('d-none');
            es.locality_selector().find('option').not(':first').remove();
            setEstablishmentContent('');
            if (!$(this).val().length) {
                return false;
            }
            ajax('action=getEstablishmentsForCountry&country=' + $(this).val(), es.messages());
        });
    },
    locality_select: function () {
        this.locality_selector().change(function () {
            ajax('action=getEstablishmentsForLocality&country=' + es.country_selector().val() + '&locality=' + $(this).val(), es.messages());
        });
    },
    ajaxables: function () {
        return $('#grant-establishments');
    },
    add_establishments_btn: function () {
        return $('#add-grant-establishments-btn');
    },
    add_establishments_click: function () {
        this.add_establishments_btn().on('click', function () {
            $('#grant-establishments').find(':checked').each(function () {
                let row = $(this).closest('tr'),
                    cloned = row.clone(),
                    deleteBtn = $('#grant-establishment-delete').html(),
                    identifier = guid();

                $(this).prop('checked', false);
                cloned.find('td:first-of-type').remove();
                cloned.find('td:last-of-type').append('<input type="hidden" name="grant_establishment[establishment_id][]" value="' + $(this).val() + '"/>');
                cloned.append('<td><input class="form-control" type="number" min="0" name="grant_establishment[pax][]" /></td>');
                cloned.append('<td>' + deleteBtn + '</td>');

                cloned.attr('data-identifier', identifier);
                cloned.find('a').attr('data-identifier', identifier).removeClass('mt-2');

                $('#grant-establishments-validated').append(cloned);

                row.addClass('d-none');
            });
        });
    },
    init: function () {
        this.country_select();
        this.locality_select();
        this.add_establishments_click();

    },
};
es.init();
