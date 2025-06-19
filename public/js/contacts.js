/*jshint esversion: 6 */

const contacts = {
    autocomplete: function (source, target) {
        source.on('change keyup paste', function () {
            contacts.format(source);
            target.val(source.val());
        });
    },
    capitalize: function (str) {
        let previousIsSpaceOrHyphen = true;
        return str.toLowerCase().split('').map(function (letter) {
            if (previousIsSpaceOrHyphen && /[a-z\u00E0-\u00FC]/.test(letter)) {
                letter = letter.toUpperCase();
                previousIsSpaceOrHyphen = false;
            } else if (letter === ' ' || letter === '-') {
                previousIsSpaceOrHyphen = true;
            } else {
                previousIsSpaceOrHyphen = false;
            }
            return letter;
        }).join('');
    },
    format: function (source) {
        source.on('change keyup paste', function () {
            $(this).val(contacts.capitalize($(this).val()));
        });
    },
};

function append_shop_doc(result) {
    let c = $('#affected_shop_docs'),
        identifier = 'shop-doc-' + guid();

    c.append('<div class="row align-items-center text-black fw-bold ' + identifier + '"><div class="col-10">' + result.term + '</div>' +
        '<div class="col-2">' +
        '<input type="hidden" name="shop_docs[]" value="' + result.entry.id + '"/>' +
        '<ul class="mfw-actions mb-2">\n' +
        '            <li data-bs-toggle="modal" data-bs-target="#destroy_shop_range_modal" data-target="' + identifier + '">\n' +
        '    <a href="#" class="btn btn-sm btn-danger" data-bs-placement="top" data-bs-title="Supprimer le document ?" data-bs-toggle="tooltip"><i class="fas fa-trash"></i></a>\n' +
        '</li></ul>' +
        '</div></div>');
}

// Affichage conditionnel du bloc Relatif aux sociétés
$('#profile_account_type').change(function () {
    let c = $('#account_type_is_not_company'), d = $('#account_type_is_company');
    if ($(this).val() !== 'company') {
        c.removeClass('d-none');
        d.addClass('invisible');
        $('#profile_company_name').val('');
    } else {
        c.addClass('d-none');
        d.removeClass('invisible');
        c.find('select').prop('selectedIndex', 0).change();
    }
});

// Affichage conditionnel du bloc A jour des cotisations de l'année
$('#profile_savant_society_id').change(function () {
    let c = $('#profile_cotisation_year_container');
    if (this.selectedIndex === 0) {
        $('#profile_cotisation_year').prop('selectedIndex', 0).change();
        c.addClass('d-none');
    } else {
        c.removeClass('d-none');
    }
});
