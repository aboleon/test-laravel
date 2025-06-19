function show_results(result) {
    console.log('executing Results', result);
    let list = '<div class="suggestions bottom"><ul>',
        c = jQuery(result.container),
        i = 0;
    if (result.items.length) {
        for (i = 0; i < result.items.length; ++i) {
            list = list.concat('<li data-id="' + result.items[i].id + '" class="flex spb val">');
            list = list.concat('<a class="w-100" href="'+result.items[i].route+'">#'+ result.items[i].id + ' ' + result.items[i].name + '</a>');
            list = list.concat('</li>');
        }
    } else {
        list = list.concat('<li class="no-effect">Aucun établissement avec ce nom <i class="fa-solid fa-check"></i></li>');
    }
    list = list.concat('</ul></div>');
    c.append(list).find('.suggestions').show();
}

$(function () {
    let nameInput = $('#search_establishement');
    nameInput.focusin(function(){
        $(this).parent().removeClass('focus-out');
    });
    nameInput.focusout(function(){
        let elem = $(this);
        setDelay(function () {
            console.log(elem);
            elem.parent().addClass('focus-out');
        }, 500);
    });
    nameInput.keyup(function () {
        let container = $('#establishment_search_result'),
            data = $(this).val();
        container.find('.suggestions').remove();
        setDelay(function () {
            if (data.length > 2) {
                let formData = 'action=searchEstablishmentBase&callback=show_results&keyword=' + data+'&container=#'+container.attr('id');
                ajax(formData, container);
            } else {
                $('.suggestions').empty();
            }
        }, 500);
    });
});
