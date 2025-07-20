@php
    if(!isset($event)) {
        throw new \Exception('Event is required for export panel');
    }
@endphp

<div class="modal fade"
     id="modal_export_panel"
     tabindex="-1"
     aria-labelledby="the_export_panel"
     aria-hidden="true"
     data-event-id="{{ $event->id }}">
    <div class="modal-dialog modal-lg">
        <form class="modal-form" data-ajax="{{route('ajax')}}">
            <input type="hidden" name="action" value="">
            <input type="hidden" name="group" value="">
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="the_export_panel">Exporter</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <p class="mb-3">Exporter:</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mode" id="modeSelection"
                                   value="selection">
                            <label class="form-check-label" for="modeSelection">
                                Sélection
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mode" id="modeAll" value="all" checked>
                            <label class="form-check-label" for="modeAll">
                                Tout
                            </label>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <p class="mb-3">Format d'export:</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatExcel"
                                   value="xlsx" checked>
                            <label class="form-check-label" for="formatExcel">
                                Excel (.xlsx)
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatCsv" value="csv">
                            <label class="form-check-label" for="formatCsv">
                                CSV (.csv)
                            </label>
                        </div>
                    </div>

                    <hr>

                    <p class="mb-3">Sélectionnez les champs à exporter:</p>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="exportSelectAll">
                        <label class="form-check-label small" for="exportSelectAll">Tout
                            sélectionner</label>
                    </div>
                    <hr>

                    {{-- Dynamic Fields Container --}}
                    <div id="dynamicFieldsContainer">
                        {{-- Fields will be dynamically loaded here --}}
                    </div>

                </div>

                <div class="messages m-3"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" class="btn btn-primary submit-btn">
                        <div class="spinner-border spinner-border-sm spinner-element"
                             role="status"
                             style="display: none;">
                            <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                        </div>
                        Exporter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script>
        // Store all field mappings in a global object
        window.exportFieldMappings = window.exportFieldMappings || {};

        // Define the callback function for handling export downloads
        window.handleExportDownload = function (result) {
            if (result.hasOwnProperty('file') && result.hasOwnProperty('filename')) {
                var format = $('input[name="exportFormat"]:checked').val();
                var mimeType = format === 'csv'
                    ? 'text/csv'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

                // Convert base64 to blob
                var blob = new Blob([new Uint8Array(atob(result.file).split('').map(function (c) {
                    return c.charCodeAt(0);
                }))], {type: mimeType});

                // Create download link
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = result.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                $('#modal_export_panel').modal('hide');
            }
        };

        function renderExportFields(fieldsMapping) {
            var html = '';

            // Single unified list header
            html += '<div class="form-group mb-4">';
            html += '<div class="d-flex justify-content-between align-items-center mb-3">';
            html += '<div>';
            html += '<span class="fw-bold">Champs à exporter</span>';
            html += '<small class="text-muted ms-2">(Les champs optionnels sont en gris)</small>';
            html += '</div>';
            html += '<div class="form-check">';
            html += '<input class="form-check-input" type="checkbox" id="exportSelectAll" checked>';
            html += '<label class="form-check-label" for="exportSelectAll">Tout sélectionner</label>';
            html += '</div>';
            html += '</div>';
            html += '<div class="px-3">';

            // Iterate through fields in the order they appear in the array
            $.each(fieldsMapping, function(fieldKey, fieldData) {
                var isOptional = fieldData.type === 'optional';
                var textClass = isOptional ? 'text-muted' : '';
                var checked = fieldData.type === 'mandatory' ? 'checked' : '';

                html += '<div class="form-check mb-2 field-item">';
                html += '<input class="form-check-input export-field" type="checkbox" ';
                html += 'id="field_' + fieldKey + '" ';
                html += 'name="exportFields[]" ';
                html += 'value="' + fieldKey + '" ';
                html += checked + ' ';
                if (fieldData.type === 'mandatory') {
                    html += 'data-mandatory="true" ';
                }
                html += '>';
                html += '<label class="form-check-label ' + textClass + '" for="field_' + fieldKey + '">';
                html += fieldData.name;
                if (fieldData.type === 'mandatory') {
                    html += ' <span class="text-danger">*</span>';
                }
                html += '</label>';
                html += '</div>';
            });

            html += '</div>';
            html += '</div>';

            // Add note about mandatory fields
            html += '<div class="text-muted small mt-2">';
            html += '<span class="text-danger">*</span> Champs obligatoires (ne peuvent pas être décochés)';
            html += '</div>';

            return html;
        }

        function bindFieldEventHandlers() {
            // Handle select all checkbox
            $('#exportSelectAll').off('change').on('change', function() {
                var isChecked = $(this).prop('checked');
                $('.export-field').each(function() {
                    // Only change non-mandatory fields
                    if (!$(this).data('mandatory')) {
                        $(this).prop('checked', isChecked);
                    }
                });
            });

            // Handle individual field changes
            $('.export-field').off('change').on('change', function() {
                // Prevent unchecking mandatory fields
                if ($(this).data('mandatory') && !$(this).prop('checked')) {
                    $(this).prop('checked', true);
                    alert('Ce champ est obligatoire et ne peut pas être décoché.');
                    return;
                }

                // Update select all checkbox state
                var totalOptionalFields = $('.export-field:not([data-mandatory="true"])').length;
                var checkedOptionalFields = $('.export-field:not([data-mandatory="true"]):checked').length;
                var allMandatoryChecked = $('.export-field[data-mandatory="true"]').length ===
                    $('.export-field[data-mandatory="true"]:checked').length;

                // Select all is checked only if all optional fields are checked (mandatory are always checked)
                $('#exportSelectAll').prop('checked',
                    totalOptionalFields === checkedOptionalFields && allMandatoryChecked
                );
            });

            // Add CSS to make mandatory checkboxes appear disabled
            $('<style>')
                .prop('type', 'text/css')
                .html('.export-field[data-mandatory="true"] { opacity: 0.8; cursor: not-allowed; }')
                .appendTo('head');
        }

        $(function() {
            // Handle export button clicks
            $('button.exportable').off('click').on('click', function() {
                let exportModal = $('#modal_export_panel');
                let exportable = $(this).data('exportable');

                if (exportModal.length && exportable) {
                    // Set the action value
                    exportModal.find('form input[name=action]').val(exportable);
                    exportModal.find('form input[name=group]').val($(this).data('group'));

                    // Get the field mappings for this exportable
                    let fieldMappingsKey = 'fieldMappings' + exportable;
                    let fieldsMapping = window[fieldMappingsKey];

                    if (fieldsMapping) {
                        // Render the fields
                        let fieldsHtml = renderExportFields(fieldsMapping);
                        $('#dynamicFieldsContainer').html(fieldsHtml);

                        // Bind event handlers
                        bindFieldEventHandlers();

                        // Check if all checkboxes are checked
                        var totalFields = $('input[name="exportFields[]"]').length;
                        var checkedFields = $('input[name="exportFields[]"]:checked').length;
                        $('#exportSelectAll').prop('checked', totalFields === checkedFields);
                    }
                }
            });

            // Modal handler
            $('#modal_export_panel').handleMultipleSelectionModal({
                noSelectionMessage: 'Veuillez sélectionner au moins un contact',
                prevalidationHandler: function (oFormData) {
                    if (!oFormData.exportFields) {
                        return 'Veuillez sélectionner au moins un champ à exporter';
                    }
                    // Only check for selection if mode is 'selection'
                    if (oFormData.mode === 'selection' && !oFormData.ids) {
                        return 'Veuillez sélectionner au moins un contact à exporter';
                    }
                },
                success: function (result) {
                    redrawDataTable();
                    return true;
                },
            });

            // remove the tab cookie for edit page
            Cookies.set('mfw_tab_redirect_primary', 'dashboard-tabpane-tab', {expires: 1});
        });
    </script>
@endpush

@pushonce('js')
    <script src="{{asset('js/handleMultipleSelectionModal.jquery.js')}}"></script>
@endpushonce
