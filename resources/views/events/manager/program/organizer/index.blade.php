@php use App\Accessors\GroupAccessor; @endphp
<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Programme</span> &raquo;
            <span>Organizer</span>
        </h2>
        <div class="d-flex align-items-center" id="topbar-actions">
            <x-mfw::simple-modal id="export-programme-modal"
                                 class="btn btn-sm btn-secondary me-2"
                                 title="Export des interventions"
                                 confirm="Exporter"
                                 cancel="Annuler"
                                 body=""
                                 callback="ajaxExportProgramme"
                                 onshow="loadExportProgrammeForm"
                                 text='Export interventions'/>
            <x-event-config-btn :event="$event"/>
            <div class="separator"></div>
        </div>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <div class="container mt-4 wg-card">
            <h4>{{__('programs.program')}}</h4>
            <x-program :event="$event" :arrows="true" :links="true" :positions="true"/>
        </div>

    </div>

    @push('callbacks')
        <script>
            function loadExportProgrammeForm() {
                console.log('loadExportProgrammeForm executed');
                let modal = $('#mfw-simple-modal');
                modal.find('.modal-body').html(
                    '<div class="wg-card">' +
                    '<input type="hidden" id="export-event-id" value="{{ $event->id }}">' +
                    '<div class="mb-3">' +
                    '<label class="form-label">Type:</label>' +
                    '<div>' +
                    '<div class="form-check form-check-inline">' +
                    '<input class="form-check-input" type="radio" name="export_format" id="format_xlsx" value="xlsx" checked>' +
                    '<label class="form-check-label" for="format_xlsx">Excel (.xlsx)</label>' +
                    '</div>' +
                    '<div class="form-check form-check-inline">' +
                    '<input class="form-check-input" type="radio" name="export_format" id="format_csv" value="csv">' +
                    '<label class="form-check-label" for="format_csv">CSV (.csv)</label>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mb-3">' +
                    '<label class="form-label">Langue:</label>' +
                    '<div>' +
                    '<div class="form-check form-check-inline">' +
                    '<input class="form-check-input" type="radio" name="locale" id="locale_fr" value="fr" checked>' +
                    '<label class="form-check-label" for="locale_fr">Français</label>' +
                    '</div>' +
                    '<div class="form-check form-check-inline">' +
                    '<input class="form-check-input" type="radio" name="locale" id="locale_en" value="en">' +
                    '<label class="form-check-label" for="locale_en">Anglais</label>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            }

            window.postAjaxExportProgramme = function(result) {
                if (result.hasOwnProperty('error')) {
                    return;
                }

                if (result.file && result.filename) {
                    // Handle file download
                    var format = $('input[name="export_format"]:checked').val();
                    var mimeType = format === 'csv'
                        ? 'text/csv'
                        : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

                    // Convert base64 to blob
                    var blob = new Blob([new Uint8Array(atob(result.file).split('').map(function(c) {
                        return c.charCodeAt(0);
                    }))], {type: mimeType});

                    // Create download link
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = result.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Close modal
                    $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                }
            }

            window.ajaxExportProgramme = function() {
                let modal = $('#mfw-simple-modal');
                modal.find('.btn-confirm').off().click(function () {
                    let eventId = $('#export-event-id').val();
                    ajax(`action=exportProgramInterventionForEvent&callback=postAjaxExportProgramme&event_id=${eventId}&` + modal.find('input[type="radio"]:checked').serialize(), modal.find('.modal-body'));
                });
            }
        </script>
    @endpush
</x-event-manager-layout>

