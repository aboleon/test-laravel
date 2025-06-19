if ('undefined' === typeof window.utils) {

    window.utils = {
        reloadWithQueryParams: function (params) {
            const currentUrl = new URL(window.location.href);
            for (let key in params) {
                if (typeof params[key] === 'object') {
                    for (let subKey in params[key]) {
                        currentUrl.searchParams.set(`${key}[${subKey}]`, params[key][subKey]);
                    }
                } else {
                    currentUrl.searchParams.set(key, params[key]);
                }
            }

            window.location.href = currentUrl.href;
        },
        reload: function () {
            window.location.reload();
        },
    };
}
