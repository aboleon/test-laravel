<x-backend-layout>
    <x-slot name="header">
        <h2>
            Comptabilité
        </h2>

        <x-back.topbar.separator route-prefix="panel.accounting"/>
    </x-slot>


    @push('css')
        <style>
            #export-global-form .modal-title {
                margin-bottom: 30px;
            }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="shadow p-4 bg-body-tertiary rounded">

                    <!-- responses message -->
                    <x-mfw::response-messages/>

                    @include('exports.global-export-ui', ['title'=>"Export tous évènements", 'action' => '', 'event' => new \App\Models\Event])

                </div>
            </div>
        </div>
    </div>
    <template id="filetypes">
        <div class="col-12">
            <x-mfw::radio label="Type d'export:" name="action"
                          :values="['generateInvoiceExport' => 'Factures', 'generateRefundExport' => 'Avoirs']"
                          default="generateInvoiceExport"/>
        </div>
    </template>
    @push('js')
        <script>
            $(function() {
               setTimeout(function() {
                   let form = $('#export-global-form');
                    form.find('.modal-body > .row').append($('#filetypes').html());
                    form.find('.filterdata').remove();
               }, 500);
            });
        </script>
    @endpush
</x-backend-layout>


