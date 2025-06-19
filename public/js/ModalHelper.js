if ('undefined' === typeof window.ModalHelper) {
  window.ModalHelper = {
    init: function(jModal) {
      jModal.on('hide.bs.modal', function() {
        jModal.find('.messages').html('');
        let jForm = jModal.find('form');
        if (jForm.length) {
          jForm[0].reset();
        }
      });
    },
    createControl: function(jModal, control = {}) {
      const controlTag = control.tag || 'input';
      const controlType = control.type || 'hidden';
      const controlName = control.name || 'id';
      const controlValue = control.value || '';
      const controlId = control.id || '';

      jModal.find(`${controlTag}[name="${controlName}"]`).remove();

      let newControl;

      if (controlTag === 'input') {
        newControl = $(`<${controlTag}>`).attr({
          type: controlType,
          name: controlName,
          value: controlValue,
        });
      } else {
        newControl = $(`<${controlTag}>`).attr({
          name: controlName,
        });
        if (controlValue) newControl.val(controlValue);
      }

      if (controlId) {
        newControl.attr('id', controlId);
      }

      jModal.find('form').append(newControl);

    },
    customize: function(jModal, options) {

      if (options.title !== undefined) {
        jModal.find('.modal-title').text(options.title);
      }

      if (options.confirmBtnText !== undefined) {
        jModal.find('.modal-footer .action-confirm').text(options.confirmBtnText);
      }

      if (options.cancelBtnText !== undefined) {
        jModal.find('.modal-footer .action-cancel').text(options.cancelBtnText);
      }
    },
  };
}
