// Callbacks
function contingentNotice() {
  let n = $('.contingent-notice');
  console.log(n.length);
  $('.contingent-row').length < 1 ? n.removeClass('d-none') : n.addClass('d-none');
}

function ajaxPostDeleteGroup(result) {
  $('.duplicate').addClass('d-none');
  if (!result.hasOwnProperty('error')) {
    setTimeout(function() {
      $(result.input.identifier).remove();
      contingentNotice();
    }, 500);
  }
}

function ajaxDeleteContingentRow() {
  $('.delete_contingent').off().on('click', function() {
    $('.messages').html('');
    let id = $(this).attr('data-model-id'),
      identifier = 'tr.contingent-row.' + $(this).attr('data-identifier');
    $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
    if (id.length < 1 || isNaN(id)) {
      $(identifier).remove();
    } else {
      ajax('action=removeContingentRow&id=' + Number(id) + '&identifier=' + identifier, $('#messages'));
    }
  });
}

// Main
const contingent = {
  container: function() {
    return $('#contigent-container');
  },
  jsmessages: function() {
    return $('#contingent-js-messages');
  },
  add: function() {
    $('#add-contingent').click(function() {

      let jContingentRow = $('#contingent-row');
      let published = jContingentRow.data('published');

      contingent.container().append(jContingentRow.html());
      let lastrow = contingent.container().find('.contingent-row').last();
      contingent.dateSelect();
      contingent.attributeUpdater(lastrow);
      contingent.roomGroupSelect({
        published: published,
      });
      $('.contingent-notice').addClass('d-none');
    });
  },
  attributeUpdater: function(target) {
    let new_id = guid();
    contingent.updateFormTags(target, new_id);

    target.addClass(new_id + ' ' + new_id).attr('data-row', new_id);
    target.find('.key').val(new_id);
  },
  updateFormTags: function(target, new_id) {
    target.find('textarea, input, select').not('.key').each(function() {
      $(this).attr('name', new_id + '[' + $(this).attr('name') + ']');
      $(this).attr('id', $(this).attr('id') + '_' + new_id);
    });
  },
  duplicateCheck: function(row) {
    console.log('duplicateCheck execution');
    contingent.jsmessages().find('.duplicate').addClass('d-none');
    $('.contingent-row').removeClass('.duplicate-row');
    let date = row.find('.datepicker').val(),
      room_group = row.find('select.room-group').val(),
      error = false;

    this.container().find('.contingent-row').not(row).each(function() {
      if ($(this).find('.datepicker').val() == date && $(this).find('select.room-group').val() == room_group) {
        error = true;
        row.addClass('duplicate-row');
        return false;
      }
    });
    console.log(error, 'duplicateCheck error');
    return error;
  },
  roomGroupSelect: function(options) {
    let published = options.published ?? null;

    $('select.room-group').off().on('change', function() {
      let selected = $(this).val(),
        row = $(this).closest('.contingent-row'),
        row_id = row.attr('data-row'),
        room_cell = row.find('.room.type'),
        content = selected === '' ? '' : $($('template#template-room-group-' + selected).prop('content')).find('div');

      if (contingent.duplicateCheck(row)) {
        console.log('Room Select detected Contingent error', contingent.jsmessages().find('.duplicate').length);
        contingent.jsmessages().find('.duplicate').removeClass('d-none');
      } else {
        console.log('Room Select NOT detected Contingent error');
      }

      // Reset set
      row.find('.rowspan').attr('rowspan', content.length > 1 ? content.length : 1);
      row.find('td').not('.rowspan').remove();
      row.find('td.deletable').remove();
      row.next('tr.subrow').remove();
      $('.subrow.contingent-row.' + row_id).remove();

      if (content !== '') {
        row.append(contingent.generateSubfields($(content[0]), row, false, {
          published: published,
        }));
        for (let i = 1; i < content.length; i++) {
          $(contingent.generateSubfields($(content[i]), row, true, {
            published: published,
          })).insertAfter($('.' + row_id).last());
        }
      }
    });
  },
  dateSelect: function() {
    $('.datepicker').off();
    setDatepicker();
    $('.datepicker').change(function() {
      let row = $(this).closest('.contingent-row');

      if (contingent.duplicateCheck(row)) {
        console.log('Date Select detected Contingent error');
        contingent.jsmessages().find('.duplicate').removeClass('d-none');
      } else {
        console.log('Date Select NOT detected Contingent error');
      }
    });
  },
  generateSubfields: function(source, row, withsubrow = false, options) {
    let published = options.published ?? null;

    let sPublished = published ? 'checked' : '';

    let row_id = row.attr('data-row'),
      subrow = '',
      room_id = source.attr('data-room-id');

    if (withsubrow) {
      subrow = '<tr class="subrow contingent-row ' + row_id + '">';
    }

    subrow = subrow.concat('<td class="room-' + room_id + ' type">' + source.text() + '</td>');
    subrow = subrow.concat('<td class="room-' + room_id + ' sell" style="max-width: 120px"><div class="input-group"><input class="form-control" type="number" min="1" step="0.1" name="' + row_id + '[rooms][' + room_id + '][sell]" /><span class="input-group-text">€</span></div></td>');
    subrow = subrow.concat('<td class="room-' + room_id + ' buy" style="max-width: 120px"><div class="input-group"><input class="form-control" type="number" min="1" step="0.1" name="' + row_id + '[rooms][' + room_id + '][buy]" /><span class="input-group-text">€</span></div></td>');
    subrow = subrow.concat('<td class="room-' + room_id + ' pec"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="' + row_id + '[rooms][' + room_id + '][pec]" id="' + row_id + '_' + room_id + '_pec"></div></td>');
    subrow = subrow.concat('<td class="room-' + room_id + ' pec-allocation" style="max-width: 120px"><div class="input-group"><input class="form-control" type="number" min="1" step="0.1" name="' + row_id + '[rooms][' + room_id + '][pec-allocation]" /><span class="input-group-text">€</span></div></td>');
    subrow = subrow.concat('<td class="room-' + room_id + ' service"><select class="form-control form-select" name="' + row_id + '[rooms][' + room_id + '][service]">' + $('#accommodation-service').html() + '</select></td>');
    subrow = subrow.concat('<td class="room-' + room_id + ' published"><div class="form-check form-switch"><input ' + sPublished + ' class="form-check-input" type="checkbox" role="switch" name="' + row_id + '[rooms][' + room_id + '][published]" id="' + row_id + '_' + room_id + '_published"></div></td>');

    if (withsubrow) {
      subrow = subrow.concat('</tr>');
    } else {
      subrow = subrow.concat('<td class="deletable rowspan align-top" style="width: 50px"><a href="#" data-model-id="' + room_id + '" data-identifier="' + row_id + '" class="btn btn-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#mfw-simple-modal" data-modal-id="delete_contingent" data-title="Suppression d\'une ligne de contingent" data-body="" data-btn-confirm="Supprimer" data-callback="ajaxDeleteContingentRow" data-btn-confirm-class="btn-danger" data-btn-cancel="Annuler">Supprimer</a></td>');
    }

    return subrow;
  },
  init: function() {
    this.dateSelect();
    $('button[form=wagaia-form]').click(function() {
      if (!contingent.jsmessages().find('.duplicate').first().hasClass('d-none')) {
        return false;
      }
    });
    this.add();
  },
};
contingent.init();
