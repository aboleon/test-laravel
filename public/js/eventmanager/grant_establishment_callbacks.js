function ajaxPostDeleteGrantBindedEstablishment(result) {
    $(result.input.identifier).remove();
}

function setEstablishmentContent(content) {
    $('#grant-establishment-messages').html('');
    $('#grant-establishments').html(content);
}

function setEstablishmentsForCountry(result) {
    let content = '',
        options = '<option value="all">Ville</option>',
        country = $('#grant_country_selector').find(':selected').text();

    if (result.hasOwnProperty('error') || !result.hasOwnProperty('establishments')) {
        return false;
    }

    let avaialable = 0,
        eligbleAlert = $('.eligible-establishments-alert');

    eligbleAlert.addClass('d-none');
    result.establishments.forEach(function (item) {

        let is_selected = isEstablishmentInValidatedTable(item.id);
        if (!is_selected) {
            avaialable += 1;
        }
        content = content.concat('<tr class="establishment-' + item.id + (is_selected ? ' d-none' : '') + '">');
        content = content.concat('<td><input type="checkbox" value="' + item.id + '" /></td>');
        content = content.concat('<td>' + item.name + '</td>');
        content = content.concat('<td>' + country + '</td>');
        content = content.concat('<td>' + item.locality + '</td>');
        content = content.concat('</tr>');
    });

    if (avaialable > 0) {

        if (result.hasOwnProperty('localities')) {
            let options = '';
            Object.entries(result.localities).forEach(function ([key, item]) {
                options += '<option value="' + item + '">' + item + '</option>';
            });
            $('#grant_locality_selector').html(options);
        }
        setEstablishmentContent(content);
    } else {
        eligbleAlert.removeClass('d-none');
    }
}

function setEstablishmentsForLocality(result) {
    let content = '',
        country = $('#grant_country_selector').find(':selected').text(),
        locality = $('#grant_locality_selector').find(':selected').text();

    let avaialable = 0,
        eligbleAlert = $('.eligible-establishments-alert');

    eligbleAlert.addClass('d-none');

    if (result.hasOwnProperty('error') || !result.hasOwnProperty('establishments')) {
        return false;
    }
    result.establishments.forEach(function (item) {

        let is_selected = isEstablishmentInValidatedTable(item.id);
        if (!is_selected) {
            avaialable += 1;
        }
        content = content.concat('<tr class="establishment-' + item.id + (is_selected ? ' d-none' : '') + '">');
        content = content.concat('<td><input type="checkbox" value="' + item.id + '" /></td>');
        content = content.concat('<td>' + item.name + '</td>');
        content = content.concat('<td>' + country + '</td>');
        content = content.concat('<td>' + locality + '</td>');
        content = content.concat('</tr>');
    });

    if (avaialable > 0) {
        setEstablishmentContent(content);
    } else {
        eligbleAlert.removeClass('d-none');
    }
}

function isEstablishmentInValidatedTable(establishmentId) {
    return $('#grant-establishments-validated tr.establishment-' + establishmentId).length;
}
