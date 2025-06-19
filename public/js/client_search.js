function appendClientList(result) {
  console.log('executing actionOnClientList', result);
  let list = '<div class="suggestions"><ul>',
    c = jQuery(result.container),
    i = 0;
  if (result.items.length) {
    for (i = 0; i < result.items.length; ++i) {
      let s = '<li data-id="' + result.items[i].id + '" ' +
        'data-first-name="' + result.items[i].first_name + '" ' +
        'data-last-name="' + result.items[i].last_name + '" ' +
        'data-email="' + result.items[i].email + '" ' +
        'data-locality="' + result.items[i].locality + '" ' +
        'data-country="' + result.items[i].country + '" ' +
        'data-function="' + result.items[i].function + '" ' +
        'data-account-type="' + result.items[i].account_type + '" ';

      if ('is_main_contact' in result.items[i]) {
        s += 'data-is-main-contact="' + result.items[i].is_main_contact + '" ';
      }

      s += ' class="flex spb val">';

      list = list.concat(s);
      list = list.concat('<div><span class="client_id">#' + result.items[i].id + '</span> ' + result.items[i].first_name + ' ' + result.items[i].last_name + (result.items[i].blacklisted == '1' ? ' <span class="badge bg-dark"> client sur liste noire</span>' : '') + '</div><div>');
      list = list.concat('</div></li>');
    }
  } else {
    list = list.concat('<li class="no-effect">Aucun résultat</li>');
  }
  list = list.concat('</ul></div>');
  c.append(list).find('.suggestions').show();
  actionOnClientList();
}


$(function() {
  $('.client-search-input').each(function() {
    let extraParam = $(this).attr('extra_param');
    let container = $(this).closest('.client-search-holder');
    $(this).keyup(function(event) {
      if (event.which === 27) {
        return;
      }


      let data = $(this).val();
      container.find('.suggestions').remove();
      setDelay(function() {
        if (data.length > 2) {
          let formData = 'action=searchClientBase&callback=appendClientList&keyword=' + data + '&container=#' + container.attr('id');
          if (extraParam) {
            formData += '&' + extraParam;
          }
          ajax(formData, container);
        } else {
          $('.suggestions').empty();
        }
      }, 500);
    });
    $(this).click(function() {
      $(this).change();
      if ($(this).val() === '') {
        container.find('.suggestions').remove();
      }
    });
  });

});
