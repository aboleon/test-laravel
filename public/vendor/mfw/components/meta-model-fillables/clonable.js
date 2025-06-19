const clonableContent = {
  clone: function () {
    $('span.btn.cloner').off().click(function () {
      let cloned = $(this).prev('.clonable').clone(),
        old_id = cloned.data('id'),
        new_id = guid(),
        container = $(this).closest('.bloc-editable');
      clonableContent.attributeUpdater(cloned, old_id, new_id);
      clonableContent.resetEditors(cloned, true);
      cloned.insertBefore($(this));
      clonableContent.resetCounters(container);
      clonableContent.removeTriggers();
      clonableContent.resetMedia();
      container.find('.i-remove').removeClass('d-none');
    });
  },
  sortable: function () {
    let bcs = $('.bloc-clonable');
    if (bcs.length) {
      bcs.each(function () {
        let bc = $(this);
        bc.sortable({
          cancel: '.not-draggable',
          stop: function () {
            tinymce.triggerSave();
            bc.find('> div.clonable').each(function (index) {
              $(this).find('.i-counter').text(index + 1);
              clonableContent.resetEditors($(this));
            });
          },
        });
      });
    }
  },
  resetMedia: function (container) {
    MediaclassUploader.init();
  },
  removeTriggers: function () {
    $('.clonable .i-remove').off().click(function () {
      let container = $(this).closest('.bloc-editable');
      $(this).closest('.clonable').remove();
      clonableContent.resetCounters(container);
    });
  },
  resetCounters: function (container) {
    let counters = container.find('.i-counter');
    if (counters.length < 2) {
      container.find('.i-remove').addClass('d-none');
    }
    counters.each(function (index) {
      $(this).text(index + 1);
    });
  },
  attributeUpdater: function (target, old_id, new_id) {

    function replace(variable) {
      return variable.replace(old_id, new_id);
    }

    target.attr('data-id', new_id);

    target.find('textarea, input, select, label').each(function () {
      let name = $(this).attr('name'),
        id = $(this).attr('id'),
        label = $(this).attr('for');
      name !== undefined ? $(this).attr('name', replace(name)) : null;
      id === undefined ? $(this).attr('id', $(this).attr('name')) : $(this).attr('id', replace(id));
      label === undefined ? $(this).attr('for', $(this).parent().find('input').attr('id')) : $(this).attr('for', replace(label));
    });
  },
  init: function () {
    this.clone();
    this.removeTriggers();
    this.sortable();
  },
  resetEditors: function (cloned, reset = false) {
    cloned.find('.tox').remove();
    if (reset) {
      cloned.find('input, textarea').val('');
    }
    let editors = cloned.find('textarea');
    if (editors.length) {
      editors.removeAttr('style');
      editors.removeAttr('aria-hidden');
      setTimeout(function () {
        editors.each(function () {
          let id = '#' + $(this).attr('id');
          if (reset === true) {
            $(this).text('');
          }
          if ($(this).hasClass('simplified')) {
            tinymce.init(mfw_simplified_tinymce_settings(id));
          }
          if ($(this).hasClass('extended')) {
            tinymce.init(mfw_default_tinymce_settings(id));
          }
        });
      }, 100);
    }

  },
};
clonableContent.init();
