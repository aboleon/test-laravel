$(function () {
    const ps = {
        el: function () {
            return $('#published_status');
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
                    $(this).removeClass('bg-success').addClass('bg-danger').text(ps.messages.pushOffline());
                } else {
                    $(this).removeClass('bg-danger').addClass('bg-success').text(ps.messages.pushOnline());
                }
            }, function () {
                if (ps.status() === 'online') {
                    $(this).removeClass('bg-danger').addClass('bg-success').text(ps.messages.isOnline());
                } else {
                    $(this).removeClass('bg-success').addClass('bg-danger').text(ps.messages.isOffline());
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
                        if (result.hasOwnProperty('success')) {
                            if (ps.status() === 'online') {
                                ps.el().attr('data-status', 'offline').text(ps.messages.isOffline()).removeClass('bg-success').addClass('bg-danger');
                            } else {
                                ps.el().attr('data-status', 'online').text(ps.messages.isOnline()).removeClass('bg-danger').addClass('bg-success');
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
