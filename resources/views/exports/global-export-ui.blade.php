<form id="export-global-form" method="POST" class="modal-form" data-ajax="{{route('ajax')}}">
    @csrf
    @php
        $defaultExportFormats = [
           'formats' => ['pdf' => 'PDF', 'zip' => 'PDFs en zip', 'csv' => 'CSV'],
           'default' => 'zip'
           ];
           $exportsFormats = $export_formats ?? $defaultExportFormats;

    @endphp
    <input type="hidden" class="filterdata" name="action" value="{{$action}}"/>
    <input type="hidden" class="filterdata" name="event_id" value="{{$event?->id}}"/>
    <div class="modal-content">
        <div class="modal-header d-flex align-items-end">
            <h4 class="modal-title fs-5" id="exampleModalLabel">{{ $title ?? 'Export'}}</h4>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ __('ui.close') }}"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <x-mfw::datepicker :label="__('ui.start')"
                                       name="start"
                                       value=""/>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <x-mfw::datepicker :label="__('ui.end')"
                                       name="end"
                                       value=""/>
                </div>

                <div class="col-12">
                    <x-mfw::radio label="Exporter en:" name="export_format"
                                  :values="$exportsFormats['formats']" :default="$exportsFormats['default'] ?? array_key_first($exportsFormats['formats']) "/>
                </div>

            </div>

            <div class="messages"></div>
        </div>
        <div class="row p-3 pt-0 align-items-center">
            <div class="col-6">
                <x-mfw::spinner text="Traitement..."/>
            </div>
            <div class="col-6 text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('ui.close') }}
                </button>
                <button id="generate_export" type="button" class="btn btn-primary submit-btn">Générer</button>
            </div>
        </div>
    </div>
</form>
@pushonce('callbacks')
    <script>
        function processExport(response) {

            console.log(response.input.container);
            if (response.hasOwnProperty('input') && response.input.hasOwnProperty('container')) {
                $(response.input.container).find('.mfw-spinner').addClass('d-none');
                console.log(response.input.container);
            }

            if (response.auto_download && response.download_url) {
                // Create hidden iframe to trigger download
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = response.download_url;
                document.body.appendChild(iframe);

                // Remove iframe after 5 seconds
                setTimeout(() => iframe.remove(), 5000);
            }
        }
    </script>
@endpushonce
@push('js')
    <script>
        $(document).ready(function () {
            $('#generate_export').off().on('click', function () {
                let form = $('#export-global-form'), spinner = form.find('.mfw-spinner');
                spinner.removeClass('d-none');
                form.find('.messages').html('');
                ajax('container=#export-global-form&' + form.find('input').serialize(), form);
            });
        });
    </script>
@endpush
