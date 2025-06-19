//----------------------------------------
// interact.js
//----------------------------------------
// A library of functions to be used across the MFW application
// depends on:
// - jQuery

// IIFE (Immediately Invoked Function Expression) ensures our library
// variables/functions do not collide with other scripts
(function(window) {
  // Check if interact is already loaded
  if (window.interact) {
    console.warn('Interact.js has already been loaded. Duplicate initialization is prevented.');
    return;
  }

  // Expose our library to the global window object
  window.interact = {
    // A function to toggle an element's visibility based on a checkbox's state
    checkboxTogglesTarget: function(checkboxSelector, targetSelector, onShowAfter) {
      if ($(checkboxSelector).is(':checked')) {
        $(targetSelector).show();
        onShowAfter && onShowAfter();
      } else {
        $(targetSelector).hide();
      }

      // Add event listener to checkbox to toggle div visibility
      $(checkboxSelector).on('click', function() {
        if ($(this).is(':checked')) {
          $(targetSelector).show();
          onShowAfter && onShowAfter();
        } else {
          $(targetSelector).hide();
        }
      });
    },
    selectTogglesTargets: function(selectSelector, targetsSelectors) {
      // Initially hide all potential target elements
      $.each(targetsSelectors, function(value, targetSelectorsArray) {
        $.each(targetSelectorsArray, function(index, targetSelector) {
          $(targetSelector).hide();
        });
      });

      // Show the targets corresponding to the initially selected option
      var initialSelectedValue = $(selectSelector).val();
      if (targetsSelectors.hasOwnProperty(initialSelectedValue)) {
        $.each(targetsSelectors[initialSelectedValue], function(index, targetSelector) {
          $(targetSelector).show();
        });
      }

      // Add an event listener to the select to toggle visibility based on selected value
      $(selectSelector).on('change', function() {
        var selectedValue = $(this).val();

        // Hide all targets
        $.each(targetsSelectors, function(value, targetSelectorsArray) {
          $.each(targetSelectorsArray, function(index, targetSelector) {
            $(targetSelector).hide();
          });
        });

        // Show the targets associated with the selected value
        if (targetsSelectors.hasOwnProperty(selectedValue)) {
          $.each(targetsSelectors[selectedValue], function(index, targetSelector) {
            $(targetSelector).show();
          });
        }
      });
    },
    selectFeedsSelect: function(sourceSelector, targetSelector, apiUrl, options) {

      options = options || {};
      let paramKey = options.paramKey || 'id';
      let itemsKey = options.itemsKey || 'items';
      let dataPayload = options.payload || {};
      let spinner = options.spinner || null;
      let addPlaceholder = options.addPlaceholder || false;
      let onChange = options.onChange || null;
      let placeholderText = options.placeholderText || '--- Choisissez ---';

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        url: apiUrl,
        type: 'POST',
        dataType: 'json',
      });

      let jSpinner = null;
      if (spinner) {
        jSpinner = $(spinner);
      }

      $(sourceSelector).on('change', function() {

        dataPayload[paramKey] = $(this).val();

        if (jSpinner) {
          jSpinner.show();
        }

        $.ajax({
          data: dataPayload,
          success: function(data) {
            const targetSelect = $(targetSelector);
            targetSelect.empty(); // clear previous options

            if (addPlaceholder) {
              targetSelect.append($('<option>', {
                value: '',
                text: placeholderText,
                disabled: true,
                selected: true,
              }));
            }

            const items = data[itemsKey];

            $.each(items, function(index, item) {
              targetSelect.append($('<option>', {
                value: index,
                text: item,
              }));
            });

            onChange && onChange(data, targetSelect);

          },
          error: function(xhr, status, error) {
            console.error('Ajax request failed: ', error);
          },
          complete: function() {
            if (jSpinner) {
              jSpinner.hide();
            }
          },
        });
      });
    },

    /**
     *
     * Warning
     * -------------
     * It also assumes the following things:
     * - the "confirm modal" is loaded
     *      @include('front.shared.confirm_modal')
     *
     * Overview
     * --------------
     * This function is used to create a context of interaction.
     * Any element with the class "gui-action" will be able to trigger an action automatically.
     * An element with the class "gui-action" is a trigger which dictates the action to be triggered.
     *
     *
     * An action can be of different types:
     * - general (by default)
     *
     * To configure the type, the data-type attribute can be used on the trigger element.
     *
     * In general, to configure an action we set attributes either on the trigger element itself (by default),
     * or on its parent element with the class "interaction-context".
     * Below is a description of the attributes to be used for each type of action.
     *
     *
     * General action
     * ======================
     * ---- attributes on the trigger element itself ----
     * - data-action: str: the action to be triggered. It must be defined as a callback in the options.actions map.
     *      If you use the ajax function (in common.js), then it's also the name of the action parameter
     *      sent to the ajax endpoint.
     * - ?data-confirm: int (0|1): whether a confirmation modal should be displayed before triggering the action
     * - ?data-param-key: str=id: the key of the first parameter to be sent to the ajax action
     *
     * ---- attributes on the parent element with the class "interaction-context" ----
     * - ?data-param-value: str: the value of the first parameter to be sent to the ajax action
     *
     *
     *
     *
     * @param jContext
     * @param options
     *    - actions: a map of actionName => functionToExecute( params)
     *        ----- params:
     *        -------- formData: str: the data to be sent to the potential ajax action
     *        -------- target: jquery element: the trigger element
     *        -------- confirmModal: jquery element: the confirmation modal
     *                (so that you can hide it for instance, with confirmModal.modal('hide') )
     *        -------- key: str: the key of the first parameter used by this action
     *        -------- value: str: the value of the first parameter used by this action
     *
     *
     *
     */
    createContext: function(jContext, options) {

      options = options || {};
      let actions = options.actions || {};

      let jConfirmModal = $('#confirm_modal');
      jConfirmModal.on('hidden.bs.modal', function() {
        jConfirmModal.find('.btn-confirm').off('click');
      });

      jContext.on('click', '.gui-action', function(e) {
        const jTarget = $(e.currentTarget);
        let key = 'id';
        let value = jTarget.closest('.interaction-context').attr('data-' + key);
        let action = jTarget.data('action');
        let confirm = jTarget.data('confirm') || false;

        let formData = 'action=' + action + '&' + key + '=' + value;
        let params = {
          target: jTarget,
          formData: formData,
          key: key,
          value: value,
          confirmModal: jConfirmModal,
        };

        let functionToExecute = actions[action] || function(p) {
          console.error('interact.createContext: No function found for action: ' + action, p);
        };

        if (confirm) {
          jConfirmModal.find('.btn-confirm').on('click', function() {
            functionToExecute(params);
            return false;
          });
          jConfirmModal.modal('show');
        } else {
          functionToExecute(params);
        }

        return false;
      });

    },
  };

})(window);

