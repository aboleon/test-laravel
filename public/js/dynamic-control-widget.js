if ('undefined' === typeof window.initDynamicControlWidget) {

    /**
     * Initialize a dynamic control widget.
     * Depending on the field chosen by the user, the widget can render various types of input controls,
     * including dropdowns, search boxes, date inputs, and more.
     * The widget integrates with external data sources through AJAX.
     *
     * See the various field types in the switch statement below.
     *
     * Dependencies:
     *
     * - js/bs-autocomplete.js
     * - flatpickr
     *
     * ===============
     * Hint: just add this to your page to take care of the dependencies:
     *
     * @pushonce('js')
     *     <script src="{{asset('js/bs-autocomplete.js')}}"></script>
     *     <script src="{{asset('js/dynamic-control-widget.js')}}"></script>
     *     <script src="{!! asset('vendor/mfw/flatpickr/flatpickr.js') !!}"></script>
     *     <script src="{!! asset('vendor/mfw/flatpickr/locale/'. app()->getLocale().'.js') !!}"></script>
     *     <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@xcash-v300/dist/latest/bootstrap-autocomplete.min.js"></script>
     * @endpushonce
     *
     *
     * @push('css')
     *     <link rel="stylesheet" href="{!! asset('vendor/mfw/flatpickr/flatpickr.min.css') !!}"/>
     *     <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
     * @endpush
     *
     */
    window.initDynamicControlWidget = function (options) {
        const defaults = {
            fieldData: {},
            selectElementId: 'fieldSelect',
            controlContainerId: 'dynamicControlContainer',
            controlId: 'dynamicControl',
            controlName: 'value',
            ajaxSelector: '#modal-form-update-panel',
            defaultDatePickrOptions: {},
            defaultDatetimePickrOptions: {},
        };
        const settings = $.extend({}, defaults, options);

        $(document).ready(function () {

            let jAjaxSelector = $(settings.ajaxSelector);

            for (let field in settings.fieldData) {
                const fieldLabel = settings.fieldData[field][0];
                $('#' + settings.selectElementId).append(new Option(fieldLabel, field));
            }

            $('#' + settings.selectElementId).on('change', function () {
                const selectedField = $(this).val();
                const fieldType = settings.fieldData[selectedField][1];
                const fieldOptions = settings.fieldData[selectedField].slice(2);

                let controlHtml = '';
                let controlHtmlAlreadyGenerated = false;
                let useFlatpickr = false;
                let useFlatpickrDatetime = false;
                let flatpickrOptions = settings.defaultDatePickrOptions;
                let flatpickrOptionsDatetime = settings.defaultDatetimePickrOptions;

                switch (fieldType) {
                    //----------------------------------------
                    // date
                    //----------------------------------------
                    case 'date':
                        controlHtml = `<input type="text" name="${settings.controlName}" class="form-control flatpickr-control" id="${settings.controlId}">`;
                        useFlatpickr = true;
                        if (fieldOptions[0]) {
                            flatpickrOptions = fieldOptions[0];
                        }
                        break;
                    //----------------------------------------
                    // dico
                    //----------------------------------------
                    case 'dico':
                    case 'dico_meta':
                        controlHtmlAlreadyGenerated = true;
                        let spinner = '<div id="dico-spinner" class="spinner-border" role="status">\n' +
                            '                <span class="visually-hidden">Loading...</span>\n' +
                            '              </div>';
                        $('#' + settings.controlContainerId).html(spinner);

                        ajax('action=selectDictionaryEntries&dictionary_slug=' + fieldOptions[0], jAjaxSelector, {
                            spinner: '#dico-spinner',
                            successHandler: function (response) {
                                let controlHtml = `<select name="${settings.controlName}" class="form-control" id="${settings.controlId}">`;
                                const items = response.items;

                                switch (fieldType) {
                                    case 'dico_meta':
                                        for (const key in items) {
                                            const group = items[key];
                                            const groupName = group.name;
                                            const groupValues = group.values;

                                            controlHtml += `<optgroup label="${groupName}">`;
                                            for (const valueKey in groupValues) {
                                                controlHtml += `<option value="${valueKey}">${groupValues[valueKey]}</option>`;
                                            }
                                            controlHtml += '</optgroup>';
                                        }
                                        break;
                                    case 'dico':
                                        for (const key in items) {
                                            controlHtml += `<option value="${key}">${items[key]}</option>`;
                                        }
                                        break;
                                }

                                controlHtml += '</select>';
                                $('#' + settings.controlContainerId).html(controlHtml);
                                return true;
                            },
                        });
                        break;

                    //----------------------------------------
                    // enum
                    //----------------------------------------
                    case 'enum':
                        controlHtml = `<select name="${settings.controlName}" class="form-control" id="${settings.controlId}">`;
                        for (const value in fieldOptions[0]) {
                            controlHtml += `<option value="${value}">${fieldOptions[0][value]}</option>`;
                        }
                        controlHtml += '</select>';
                        break;
                    //----------------------------------------
                    // fk
                    //----------------------------------------
                    case 'fk':
                        controlHtml = `<select name="${settings.controlName}" class="form-control" id="${settings.controlId}"></select>`;
                        $('#' + settings.controlContainerId).html(controlHtml);
                        controlHtmlAlreadyGenerated = true;
                        ajax('action=' + fieldOptions[0], jAjaxSelector, {
                            successHandler: function (result) {
                                let items = result.items;
                                let $select = $('#' + settings.controlId);
                                items.forEach(function (item) {
                                    $select.append($('<option>', {
                                        value: item.value,
                                        text: item.label,
                                    }));
                                });
                            },
                        });
                        break;
                    case 'custom':
                        switch (fieldOptions[0]) {
                            case 'getParticipationTypes':
                                controlHtmlAlreadyGenerated = true;
                                console.log('getParticipationTypes modif', $('#' + settings.controlId), $('#modal_select_participation_types select'));
                                $('#' + settings.controlContainerId).html($('#modal_select_participation_types select').clone());
                                $('#' + settings.controlContainerId).find('select').attr('name', 'participation_type_id').change();

                                break;
                        }

                        break;
                    //----------------------------------------
                    // bool
                    //----------------------------------------
                    case 'bool':
                        controlHtml = `<select name="${settings.controlName}" class="form-control" id="${settings.controlId}">`;
                        controlHtml += `<option value="1">Oui</option>`;
                        controlHtml += `<option value="0">Non</option>`;
                        controlHtml += '</select>';
                        break;
                    //----------------------------------------
                    // int
                    //----------------------------------------
                    case 'int':
                        controlHtml = `<input name="${settings.controlName}" type="number" class="form-control" id="${settings.controlId}">`;
                        break;
                    //----------------------------------------
                    // nullable date
                    //----------------------------------------
                    case 'nullable_date':
                    case 'nullable_datetime':
                        const defaultLabelNoDate = fieldOptions[0] || 'Pas de date';
                        const defaultLabelDate = fieldOptions[1] || 'Date';
                        controlHtml = `
        <div class="nullable-date-group">
            <div class="form-check">
                <input type="radio" class="form-check-input" name="nullableDateOption" id="noDateOption" checked>
                <label class="form-check-label" for="noDateOption">${defaultLabelNoDate}</label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" name="nullableDateOption" id="withDateOption">
                <label class="form-check-label" for="withDateOption">${defaultLabelDate}</label>
                <input type="hidden" name="value" value="" disabled>
                <input name="${settings.controlName}" type="date" class="form-control  flatpickr-control" id="${settings.controlId}" disabled>
            </div>
        </div>
    `;

                        if ('nullable_date' === fieldType) {
                            useFlatpickr = true;
                        } else if ('nullable_datetime' === fieldType) {
                            useFlatpickrDatetime = true;
                        }

                        if (fieldOptions[2]) {
                            flatpickrOptions = fieldOptions[2];
                            flatpickrOptionsDatetime = fieldOptions[2];
                        }

                        $(document).on('change', 'input[name="nullableDateOption"]', function (e) {
                            let jElementAlt;

                            let jElement = $('#' + settings.controlId);
                            if (
                                (true === useFlatpickr && true === flatpickrOptions.altInput) ||
                                (true === useFlatpickrDatetime && true === flatpickrOptionsDatetime.altInput)
                            ) {
                                let jTarget = $(e.target).closest('.nullable-date-group');
                                jElement = jTarget.find('.input');

                                if (true === useFlatpickrDatetime) {
                                    jElementAlt = jTarget.find('[name="value"]');
                                }
                            }

                            if ($('#withDateOption').prop('checked')) {
                                if (true === useFlatpickrDatetime) {
                                    jElementAlt.prop('disabled', false);
                                    /**
                                     *  For some reason, alt field wasn't picked up by the jForm.serialize call,
                                     *  so i created a third element with the same name as the alt field.
                                     */
                                    jElementAlt.val(jElement.val());
                                }
                                jElement.prop('disabled', false);
                            } else {
                                if (true === useFlatpickrDatetime) {
                                    jElementAlt.prop('disabled', true);
                                }
                                jElement.prop('disabled', true);
                            }
                        });
                        break;

                    //----------------------------------------
                    // search (autocomplete)
                    //----------------------------------------
                    case 'search':
                        controlHtml = '<select ' +
                            `name="${settings.controlName}" ` +
                            'placeholder="Rechercher..."' +
                            `class="form-control" id="${settings.controlId}" autocomplete="off"></select>`;
                        $('#' + settings.controlContainerId).html(controlHtml);
                        controlHtmlAlreadyGenerated = true;
                        initBsAutocomplete(settings.controlId, {
                            actionName: fieldOptions[0],
                            ajaxSelector: jAjaxSelector,
                        });
                        break;
                    //----------------------------------------
                    // text
                    //----------------------------------------
                    case 'text':
                        controlHtml = `<textarea name="${settings.controlName}" class="form-control" id="${settings.controlId}" rows="4"></textarea>`;
                        break;
                    //----------------------------------------
                    // year
                    //----------------------------------------
                    case 'year':
                        const minAttr = fieldOptions[0] ? ` min="${fieldOptions[0]}"` : '';
                        const maxAttr = fieldOptions[1] ? ` max="${fieldOptions[1]}"` : '';

                        controlHtml = `<input name="${settings.controlName}" type="number"${minAttr}${maxAttr} class="form-control" id="${settings.controlId}">`;
                        break;

                    default:
                        controlHtml = `<input name="${settings.controlName}" type="text" class="form-control" id="${settings.controlId}">`;
                }

                if (false === controlHtmlAlreadyGenerated) {
                    $('#' + settings.controlContainerId).html(controlHtml);
                }
                if (true === useFlatpickr) {
                    $('.flatpickr-control').flatpickr(flatpickrOptions);
                }
                if (true === useFlatpickrDatetime) {
                    $('.flatpickr-control').flatpickr(flatpickrOptionsDatetime);
                }
            });
        });
    };

}
