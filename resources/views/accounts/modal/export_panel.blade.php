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
                                   value="selection" checked>
                            <label class="form-check-label" for="modeSelection">
                                Sélection
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mode" id="modeAll" value="all">
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

        // Function to render fields based on field mappings
        function renderExportFields(fieldsMapping) {
            var mandatoryFields = {};
            var optionalFields = {};

            // Separate fields by type
            $.each(fieldsMapping, function(fieldKey, fieldData) {
                if (fieldData.type === 'mandatory') {
                    mandatoryFields[fieldKey] = fieldData.name;
                } else {
                    optionalFields[fieldKey] = fieldData.name;
                }
            });

            var html = '';

            // Mandatory Fields Section
            if (Object.keys(mandatoryFields).length > 0) {
                html += '<div class="form-group mb-4">';
                html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                html += '<span class="fw-bold">Champs obligatoires</span>';
                html += '<input class="form-check-input group-toggle" type="checkbox" id="mandatory-fields" data-group="mandatory-fields" checked>';
                html += '</div>';
                html += '<div class="px-3">';

                $.each(mandatoryFields, function(fieldKey, fieldLabel) {
                    html += '<div class="form-check mb-2 field-item" data-group="mandatory-fields">';
                    html += '<input class="form-check-input mandatory-field" type="checkbox" id="' + fieldKey + '" name="exportFields[]" value="' + fieldKey + '" checked>';
                    html += '<label class="form-check-label small" for="' + fieldKey + '">' + fieldLabel + '</label>';
                    html += '</div>';
                });

                html += '</div></div>';
            }

            // Add separator if both sections exist
            if (Object.keys(mandatoryFields).length > 0 && Object.keys(optionalFields).length > 0) {
                html += '<hr>';
            }

            // Optional Fields Section
            if (Object.keys(optionalFields).length > 0) {
                html += '<div class="form-group">';
                html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                html += '<span class="fw-bold">Champs optionnels</span>';
                html += '<input class="form-check-input group-toggle" type="checkbox" id="optional-fields" data-group="optional-fields">';
                html += '</div>';
                html += '<div class="px-3">';

                $.each(optionalFields, function(fieldKey, fieldLabel) {
                    html += '<div class="form-check mb-2 field-item" data-group="optional-fields">';
                    html += '<input class="form-check-input optional-field" type="checkbox" id="' + fieldKey + '" name="exportFields[]" value="' + fieldKey + '">';
                    html += '<label class="form-check-label small" for="' + fieldKey + '">' + fieldLabel + '</label>';
                    html += '</div>';
                });

                html += '</div></div>';
            }

            return html;
        }

        // Bind field event handlers
        function bindFieldEventHandlers() {
            // Handle select all checkbox
            $('#exportSelectAll').off('change').on('change', function () {
                var isChecked = $(this).prop('checked');
                $('input[name="exportFields[]"]').prop('checked', isChecked);
                $('.group-toggle').prop('checked', isChecked);
            });

            // Handle individual field changes
            $('input[name="exportFields[]"]').off('change').on('change', function () {
                // Update select all
                var totalFields = $('input[name="exportFields[]"]').length;
                var checkedFields = $('input[name="exportFields[]"]:checked').length;
                $('#exportSelectAll').prop('checked', totalFields === checkedFields);

                // Update group toggle
                var group = $(this).closest('.field-item').data('group');
                var groupTotal = $('.field-item[data-group="' + group + '"] input').length;
                var groupChecked = $('.field-item[data-group="' + group + '"] input:checked').length;
                $('#' + group).prop('checked', groupTotal === groupChecked);
            });

            // Handle group toggle
            $('.group-toggle').off('change').on('change', function () {
                var isChecked = $(this).prop('checked');
                var group = $(this).data('group');
                $('.field-item[data-group="' + group + '"] input').prop('checked', isChecked);

                // Update select all
                var totalFields = $('input[name="exportFields[]"]').length;
                var checkedFields = $('input[name="exportFields[]"]:checked').length;
                $('#exportSelectAll').prop('checked', totalFields === checkedFields);
            });
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
