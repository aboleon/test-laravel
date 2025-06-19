/*jshint esversion: 6 */

// Gère la Famille des prestations dans le Tab Config
const services = {
    container: function () {
        return $('table#services');
    },
    manage: function () {
        this.container().find('.service-selector :checkbox').click(function () {
            let ch = $(this),
                input = ch.closest('tr').find('input[type=number]').first();
            if(!$(this).closest('tr').find('.unlimited :checkbox').is(':checked')) {
                input.prop('disabled', !ch.is(':checked'));
            }
        });

        this.container().find('.unlimited :checkbox').click(function () {
            let ch = $(this),
                input = ch.closest('tr').find('input[type=number]').first();
            console.log(ch.is(':checked'));
            input.prop('disabled', ch.is(':checked'));
        });
    },
    init: function () {
        this.manage();
    },
};

services.init();

// Gère le tab Exposants
const shopping_mode = {
    selectors: function () {
        return $('#shopping_modes :radio');
    },
    customMode: function () {
        return $('#shopping_mode_custom');
    },
    customRows: function () {
        return this.customMode().find('.rows');
    },
    notice: function () {
        return this.customMode().find('.mfw-notice');
    },
    toggle: function () {
        this.selectors().click(function () {
            let mode = $(this).val();
            $('.submodes').addClass('d-none');
            $('#shopping_mode_' + mode).removeClass('d-none');

        });
    },
    addRange: function () {
        $('#add_custom_shopping_fee').off().click(function () {
            let identifier = 'shop-range-' + guid();
            if (!shopping_mode.customRows().length) {
                shopping_mode.customMode().prepend('<div class="rows"></div>');
            }
            shopping_mode.notice().addClass('d-none');
            shopping_mode.customRows().append($('#shopping_mode_custom_template').html());
            shopping_mode.customRows().find('.row').last().addClass(identifier).find('li').attr('data-target', identifier);
        });
    },
    init: function () {
        this.toggle();
        this.addRange();
    },
};
shopping_mode.init();

let shop_modal = $('#destroy_shop_range_modal');

shop_modal.on('show.bs.modal', function (event) {
    $('#shop_range_modalSave').off().on('click', function () {
        $('.' + $(event.relatedTarget).attr('data-target')).remove();
        shop_modal.modal('hide');
    });
});

shop_modal.on('hide.bs.modal', function () {
    if (shopping_mode.customRows().find('> div').length === 0) {
        shopping_mode.notice().removeClass('d-none');
    }
});

// Gère le tab Config
const esyncer = {
    initiators: function () {
        return $('.event_distributor');
    },
    pattern: function (word) {
        return new RegExp(word + '.*?', 'g');
    },
    sync: function () {
        this.initiators().each(function () {
            let c = $(this);
            c.find(':checkbox').off().click(function () {
                // li tags
                if (c.find('li').length) {
                    esyncer.sync_ul_list(c, $(this));
                }
                // div tags
                else {
                    esyncer.sync_div_list(c, $(this));
                }
            });
        });
    },
    sync_ul_list: function (container, checkbox) {
        if (checkbox.is(':checked')) {
            let cloned = checkbox.closest('li').clone();
            cloned.find(':checkbox').removeAttr('checked');
            let content = esyncer.replaceTags(cloned, container, container.data('target'));
            cloned.html(content).appendTo($('#' + container.data('target')));
        } else {
            $('#' + container.data('target')).find('li[data-id=' + checkbox.closest('li').attr('data-id') + ']').remove();
        }
    },
    sync_div_list: function (container, checkbox) {
        let c = checkbox.closest('div.item'),
            targets = container.data('target').split(',');

        if (checkbox.is(':checked')) {
            // Child element
            if (c.hasClass('child')) {

                targets.forEach(item => {

                    let target = $('#' + item),
                        parent_tag = target.find('div[data-id=' + c.data('parent') + ']');
                    if (!parent_tag.length) {
                        // Append parent tag if none
                        let cloned_parent = container.find('div[data-id=' + c.data('parent') + ']').clone();
                        cloned_parent.find(':checkbox').remove();
                        cloned_parent.html(esyncer.replaceTags(cloned_parent, container, item)).appendTo(target);

                        parent_tag = target.find('div[data-id=' + c.data('parent') + ']');
                    }
                    // Append checkable target
                    if (!target.find('div[data-id=' + c.data('id') + ']').length) {
                        let cloned = c.clone();
                        cloned.html(esyncer.replaceTags(cloned, container, item)).insertAfter(parent_tag);
                    }

                });

            }
            // Parent element
            else {
                container.find('div[data-parent=' + c.data('id') + ']').find(':checkbox').trigger('click');
            }
            // unsync
        } else {
            if (c.hasClass('child')) {

                targets.forEach(item => {
                    let target = $('#' + item);
                    target.find('div[data-id=' + c.data('id') + ']').remove();
                    let other_children = target.find('div[data-parent=' + c.data('parent') + ']');
                    if (!other_children.length) {
                        target.find('div[data-id=' + c.data('parent') + ']').remove();
                    }
                });

            } else {
                container.find('div[data-parent=' + c.data('id') + ']').find(':checked').trigger('click');
            }
        }
    },
    replaceTags: function (cloned, container, item) {
        return cloned.html().replace(esyncer.pattern(container.attr('id')), item);
    },
    init: function () {
        this.sync();
    },
};
esyncer.init();

const participation_type_syncer = {
    container: function () {
        return $('#event_participations');
    },
    checkbox: function () {
        return this.container().find(':checkbox');
    },
    targets: function () {
        return this.container().data('target').split(',');
    },
    groups: function () {
        return [...new Set(Array.from(this.checkbox()).map(el => el.getAttribute('data-parent')))];
    },
    sync: function () {
        this.checkbox().off().click(function () {
            let checked = $(this).is(':checked'),
                value = $(this).val();
            participation_type_syncer.targets().forEach(item => {
                let c = $('#' + item),
                    target = c.find('.item' + value);
                if (checked) {
                    target.removeClass('d-none').addClass('visible').find(':checkbox');
                } else {
                    target.addClass('d-none').removeClass('visible').find(':checkbox').prop('checked', false);
                }
                participation_type_syncer.manageLabels(c);
            });
        });
    },
    manageLabels: function (c) {
        this.groups().forEach(group => {
            if (c.find('div.' + group + '.visible').length > 0) {
                c.find('strong.' + group).removeClass('d-none');
            } else {
                c.find('strong.' + group).addClass('d-none');
            }
        });
    },
    manageLabelsInit: function () {
        this.targets().forEach(item => {
            participation_type_syncer.manageLabels($('#' + item));
        });
    },
    init: function () {
        this.manageLabelsInit();
        this.sync();
    },
};
participation_type_syncer.init();

$('#pec_participations, #transport_participations').find('.fw-bold :checkbox').remove();

const append_shopdoc_from_select = {
    selector: function () {
        return $('#add_shop_doc_from_select');
    },
    target: function () {
        return $('#affected_shop_docs');
    },
    source: function () {
        return $('#event_select__documents_for_exhibitors');
    },
    add: function () {

        let identifier = 'shop-doc-' + guid();

        this.selector().click(function () {
            append_shopdoc_from_select.target().find('.alert').remove();
            let selected = Number(append_shopdoc_from_select.source().val());
            if (selected === 0) {
                append_shopdoc_from_select.target().append('<div class="alert alert-danger">Pour affecter un document, faites un choix dans la liste.</div>');
                return false;
            }
            if (append_shopdoc_from_select.target().find('.row.shop-doc-' + selected).length) {
                append_shopdoc_from_select.target().append('<div class="alert alert-danger">Vous avez déjà affecté ce document.</div>');
                return false;
            }
            append_shopdoc_from_select.target().append(
                '<div class="row shop-doc-' + selected + ' align-items-center text-black fw-bold ' + identifier + '">' +
                '<div class="col-10">' + append_shopdoc_from_select.source().find(':selected').text() + '</div>' +
                '<div class="col-2">' +
                '<input type="hidden" name="shop_docs[]" value="' + selected + '"/>' +
                '<ul class="mfw-actions mb-2">\n' +
                '            <li data-bs-toggle="modal" data-bs-target="#destroy_shop_range_modal" data-target="' + identifier + '">\n' +
                '    <a href="#" class="btn btn-sm btn-danger" data-bs-placement="top" data-bs-title="Supprimer le document ?" data-bs-toggle="tooltip"><i class="fas fa-trash"></i></a>\n' +
                '</li></ul>' +
                '</div></div>');

        });
    },
};
append_shopdoc_from_select.add();

let transfert_activation = $('#transfert_activation'),
    transport_participations_options = $('#tpc');
transfert_activation.find(':checkbox').click(function() {
    if ($(this).is(':checked')) {
        transport_participations_options.removeClass('d-none');
    } else {
        transport_participations_options.addClass('d-none');
    }
});
