<div class="modal fade"
     id="modal_export"
     tabindex="-1"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <form id="export-global-form" action="{{ route('pdf-globalExport') }}" method="POST" class="modal-form" data-ajax="{{route('ajax')}}">
            @csrf
            <input type="hidden" name="action" value="{{$action}}" />
            <input type="hidden" name="event_id" value="{{$event->id}}" />
            <div class="modal-content">
                <div class="modal-header d-flex align-items-end">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Export Global PDF</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-mfw::datepicker :label="__('ui.start')"
                                               name="start"
                                               value="" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-mfw::datepicker :label="__('ui.end')"
                                               name="end"
                                               value="" />
                        </div>
                    </div>

                    <div class="messages m-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.close') }}
                    </button>
                    <button id="generate_export" type="button" class="btn btn-primary submit-btn">Générer</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('#generate_export').off().on('click', function(){
                let form = $(this).closest('form');
                let action = form.find('input[name="action"]').val();
                ajax('action='+ action + '&data='+form.serialize(), form);
            });
        });

        function exportGlobalPdf(){
            $('#export-global-form').submit();
        }
    </script>
@endpush
