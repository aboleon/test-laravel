if ('undefined' === typeof window.initBsAutocomplete) {

  /**
   * Initialize Bootstrap autocomplete
   *
   * This depends on bootstrap-autocomplete plugin.
   * Doc: https://bootstrap-autocomplete.readthedocs.io/en/latest
   *
   * You should load this plugin using this:
   * <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@xcash-v300/dist/latest/bootstrap-autocomplete.min.js"></script>
   *
   * Read this to know why:
   * https://github.com/xcash/bootstrap-autocomplete/issues/108
   */

  window.initBsAutocomplete = function(elementId, options) {

    let actionName = options.actionName ?? 'searchSomething';
    let extraQueryString = options.extraQueryString ?? '';
    let onSelectCallback = options.onSelectCallback ?? null;
    let defaultData = options.defaultData ?? null;
    let ajaxSelector = options.ajaxSelector ?? null;

    function debounce(func, wait) {
      let timeout;
      return function(...args) {
        const later = () => {
          clearTimeout(timeout);
          func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    $(`#${elementId}`).autoComplete({
      resolver: 'custom',
      preventEnter: true, // or false based on your needs
      minLength: 2, // Minimum characters before triggering search
      events: {
        search: debounce(function(qry, callback) {
          let formData = `action=${actionName}&q=${qry}`;
          if (extraQueryString) {
            formData += `&${extraQueryString}`;
          }
          ajax(formData, ajaxSelector, {
            successHandler: function(result) {
              callback(result.items);
            },
          });
        }, 300),
      },
    });

    if (onSelectCallback) {
      $(`#${elementId}`).on('autocomplete.select', function(evt, item) {
        onSelectCallback(item);
      });
    }

    if (defaultData) {
      $(`#${elementId}`).autoComplete('set', defaultData);
    }
  };
}