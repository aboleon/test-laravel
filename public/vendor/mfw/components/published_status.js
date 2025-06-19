$(function () {
  const ps = {
    el: function () {
      return $('#mfw-published_status');
    },
    _token: function () {
      return $('meta[name="csrf-token"]').attr('content');
    },
    messages: {
      pushOnline: function () {
        return ps.el().data('label-pushonline');
      },
      pushOffline: function () {
        return ps.el().data('label-pushoffline');
      },
      isOnline: function () {
        return ps.el().data('label-isonline');
      },
      isOffline: function () {
        return ps.el().data('label-isoffline');
      },
    },
    status: function () {
      return ps.el().attr('data-status');
    },
    hover: function () {
      this.el().hover(function () {
        if (ps.status() === 'online') {
          $(this).find('button').removeClass('btn-success').addClass('btn-danger').text(ps.messages.pushOffline());
        } else {
          $(this).find('button').removeClass('btn-danger').addClass('btn-success').text(ps.messages.pushOnline());
        }
      }, function () {
        if (ps.status() === 'online') {
          $(this).find('button').removeClass('btn-danger').addClass('btn-success').text(ps.messages.isOnline());
        } else {
          $(this).find('button').removeClass('btn-success').addClass('btn-danger').text(ps.messages.isOffline());
        }
      });
    },
    ajax: function () {
      this.el().click(function () {
        $.ajax({
          url: ps.el().data('ajax-url'),
          type: 'POST',
          data: 'action=publishedStatus&_token=' + ps._token() + '&from=' + ps.status() + '&id=' + ps.el().attr('data-id') + '&class=' + ps.el().attr('data-class'),
          success: function (result) {
            console.log(result);
            if (result.hasOwnProperty('success')) {
              if (ps.status() === 'online') {
                ps.el().attr('data-status', 'offline').find('button').text(ps.messages.isOffline()).removeClass('btn-success').addClass('btn-danger');
              } else {
                ps.el().attr('data-status', 'online').find('button').text(ps.messages.isOnline()).removeClass('btn-danger').addClass('btn-success');
              }
            }
          },
          error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
          },
        });
      });
    },
    init: function () {
      this.hover();
      this.ajax();
    },
  };
  ps.init();
});
