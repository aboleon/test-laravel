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

            #export-global-form .btn-close,
            #export-global-form .btn-secondary {
                display: none;
            }
        </style>
    @endpush

    <div class="shadow p-4 bg-body-tertiary rounded">

        <!-- responses message -->
        <x-mfw::response-messages/>

        <div class="container-fluid">
            <div class="row gx-5">
                <div class="col-lg-6">
                    @include('exports.global-export-ui', ['title'=>"Export tous évènements", 'action' => '', 'event' => new \App\Models\Event])
                </div>
                <div
                    class="col-lg-6 wg-card text-dark border border-secondary-subtle border-start-1 border-end-0 border-top-0 border-bottom-0"
                    data-ajax="{{ route('ajax') }}"
                    id="sage-exports">
                    <h4>Exports Sage</h4>
                    <div class="d-block pt-2">
                        <x-mfw::select name="event" label="Évènement"
                                       :values="\App\Models\Event::orderByDesc('starts')->with('texts')->get()->pluck('texts.name', 'id')->toArray()"/>
                    </div>

                    <b class="d-block pt-4 pb-2">Export de :</b>
                    <ul>
                        <li>Articles</li>
                        <li>Factures</li>
                        <li>Règlements</li>
                    </ul>

                    <div class="d-block mt-3 text-end">
                        <button type="button" class="btn btn-success">Export Sage</button>
                    </div>
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
    @include('lib.select2')
    @push('js')
        <script>
            $(function () {

                setTimeout(function () {
                    let form = $('#export-global-form');
                    form.find('.modal-body > .row').append($('#filetypes').html());
                    form.find('.filterdata').remove();

                    let sage = $('#sage-exports');
                    sage.find('select.form-control').select2();

                }, 500);

                $('#sage-exports button').off().on('click', function () {
                    ajax('action=sageExports&event_id=' + $('#sage-exports select').val(), $('#sage-exports'));
                });
            });
        </script>
    @endpush
</x-backend-layout>


