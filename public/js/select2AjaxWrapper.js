/**
 * Goal: Wrapper for select2 with ajax.
 * I found it very hard to populate select2 with default values.
 * This wrapper makes it easy to populate select2 with default values, when using ajax data source.
 *
 *
 * Dependencies: select2
 *
 *     @pushonce('js')
 *         <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 *     @endpushonce
 *
 *     @pushonce('css')
 *         <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
 *               rel="stylesheet" />
 *     @endpushonce
 */
(function($) {
  $.fn.select2AjaxWrapper = function(options) {
    let defaultValues = options.defaultValues || {};
    delete options.defaultValues;

    this.select2(options);

    if (Object.keys(defaultValues).length > 0) {
      for (let id in defaultValues) {
        var newOption = new Option(defaultValues[id], id, true, true);
        this.append(newOption).trigger('change');
      }
    }
    return this;
  };
})(jQuery);
