<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2>
            Cautions
        </h2>


        <x-back.topbar.list-combo
                :export="true"
                :export-route="route('panel.manager.event.event_deposit.export', ['event' => $event, 'status' => request('status')])"
                :show-create-route="false"
                :event="$event" />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded datatable-context datatable-not-clickable"
         data-ajax="{{route("ajax")}}">
        <x-mfw::response-messages />
        {!! $dataTable->table()  !!}
    </div>

    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}

        <script>


            $(document).ready(function() {

                // Envoyer une demande de paiement
                $('#order_sellable_deposit-table').on('draw.dt', function () {
                    $('.paymentCallModal .btn-confirm').off().click(function(e) {
                        e.preventDefault();
                        let c = $(this).closest('.paymentCallModal'), form = c.find('form')
                        ajax('action=sendEventDepositPaymentMail&'+ form.find('input').serialize(), c.find('.modal-body'));
                    });
                });

                const jContext = $('.datatable-context');

                jContext.on('click', function(e) {
                    const jTarget = $(e.target);
                    if (jTarget.hasClass('action-reimburse')) {
                        const jSpinner = jTarget.find('.spinner');
                        const edId = jTarget.data('id');
                        ajax(`action=reimburseEventDeposit&callback=reloadDatable&id=${edId}`, jContext, {
                            spinner: jSpinner
                        });

                        return false;
                    }
                    else if (jTarget.hasClass('action-make-invoice')) {
                        const jSpinner = jTarget.find('.spinner');
                        const edId = jTarget.data('id');
                        ajax(`action=makeInvoiceForEventDeposit&callback=reloadDatable&id=${edId}`, jContext, {
                            spinner: jSpinner
                        });

                        return false;
                    }
                });

                ExportDatatable.init();
            });

            const ExportDatatable = {
                btnSelector: '#topbar-actions .export',
                searchInput: '.dataTables_filter input[type="search"]',
                init: function () {
                    this.bindButton();
                },
                bindButton: function(){
                    $(this.btnSelector).off().bind('click', function(e){
                        e.preventDefault()
                        let url = new URL($(this).attr('href'));
                        let search = $(ExportDatatable.searchInput).val();

                        if (search) {
                            url.searchParams.set('search', search);
                        }

                        window.location.href = url.toString();
                        return true;
                    });
                },
            };
        </script>
    @endpush


</x-event-manager-layout>
