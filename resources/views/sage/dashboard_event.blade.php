<x-event-manager-layout :event="$event">
    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2>
            SAGE
        </h2>

        <div class="d-flex align-items-center gap-1" id="topbar-actions">
                <x-event-config-btn :event="$event"/>
            <div class="separator"></div>
        </div>
    </x-slot>


        @push('css')
            <style>
                #export-global-form .modal-title {
                    margin-bottom: 30px;
                }
            </style>
        @endpush

            <div class="row">
                <div class="col-12">
                    <div class="shadow p-4 bg-body-tertiary rounded">

                        <!-- responses message -->
                        <x-mfw::response-messages/>

                        @include('exports.global-export-ui', ['export_formats' => ['formats' =>['txt' => 'Format texte']],'title'=>"Exports", 'action' => '', 'event' => new \App\Models\Event])

                    </div>
                </div>
            </div>
        <template id="filetypes">
            <div class="col-12">
                <x-mfw::radio label="Type d'export:" name="action"
                              :values="['sageInvoiceExport' => 'Factures', 'sageArticlesExport' => 'Articles']"
                              default="sageInvoiceExport"/>
            </div>
        </template>
        @push('js')
            <script>
                $(function() {
                    setTimeout(function() {
                        let form = $('#export-global-form');
                        form.find('.modal-body > .row').append($('#filetypes').html());
                        form.find('.filterdata[name=action]').remove();
                    }, 500);
                });
            </script>
        @endpush

</x-event-manager-layout>
