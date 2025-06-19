function appendDymanicDictionnaryEntry(result) {
    console.log('Exect.appendDymanicDictionnaryEntry');
    if (!result.hasOwnProperty('error')) {
        let selectable = $('#' + result.dict_select_id);
        selectable.append('<option value="' + result.entry.id + '">' + result.term + '</option>');
        selectable.find('option').last().prop('selected', 1).change();
        setTimeout(() => {
            selectable.closest('.mfw-holder').find('.dict-dynamic-form').remove();
        }, 1600);

        if (result.hasOwnProperty('subcallback') && typeof window[result.subcallback] === 'function') {
            window[result.subcallback](result);
        }
    }
}

function rebindDictionary()
{
    dictdynamic.init();
}

const dictdynamic = {
    add: function () {
        $('span.dict-dynamic').off().click(function () {
            $('.dict-dynamic-form').remove();

            let c = $(this).closest('.mfw-holder'),
                subform = c.find('.dict-dynamic-form');
            if (subform.length) {
                return false;
            }
            c.append($('#dict-dynamic-template').html());
            dictdynamic.save();
            dictdynamic.cancel();

            c.find('.dict-dynamic-form').first().prepend('<b class="d-block mb-2">Nouvelle entrée pour ' + c.find('select').prev('label').text() + '</b>');

        });
    },
    save: function () {
        let save = $('.dict-dynamic-form').find('.save');

        save.off().click(function () {
            let c = $(this).closest('.mfw-holder');

            ajax('action=addDictionnaryEntry&dict=' +
                c.find('.dict-dynamic').first().data('dict') +
                '&dict_select_id=' + c.find('select').first().attr('id') +
                '&subcallback=' + c.data('callback') +
                '&' + $(this).closest('.dict-dynamic-form').find('input').serialize(),
                $(this).closest('.dict-dynamic-form'));

        });
    },
    cancel: function () {
        let cancel = $('.dict-dynamic-form').find('.cancel');

        cancel.off().click(function () {
            $(this).closest('.dict-dynamic-form').remove();
        });
    },
    init: function () {
        this.add();
    },
};

dictdynamic.init();
