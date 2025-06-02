<div id="deposits">

    @php
        $deposits = $accommodation->deposits;
            $error = $errors->any();
            if ($errors->any()) {
                if (old('deposit')) {
                    $d = old('deposit');

                $deposits = collect();
                    for($i=0;$i<count($d['amount']);$i++) {

                        try {
                            $paid_at = \Carbon\Carbon::createFromFormat('d/m/Y', $d['paid_at'][$i]);
                        } catch (Throwable) {
                            $paid_at = null;
                        }
                        $deposits->push(new \App\Models\EventManager\Accommodation\Deposit([
                        'amount' => $d['amount'][$i],
                        'paid_at' => $paid_at
                        ])
                        );
                    }
                }
            }
    @endphp
    @if ($deposits->isEmpty())
        <x-mfw::notice message="Aucun acompte n'est saisi"/>
    @else
        <div class="rows">
            @foreach($deposits as $deposit)
                <x-accommodation-deposit :deposit="$deposit"/>
            @endforeach
        </div>
    @endif
    <button class="btn btn-sm btn-success mt-3" id="add_deposit" type="button">Ajouter</button>
    <template id="deposit_template">
        <x-accommodation-deposit :deposit="(new \App\Models\EventManager\Accommodation\Deposit())"/>
    </template>
</div>
<div class="modal fade" id="destroy_deposit_modal" tabindex="-1" aria-labelledby="deposit_modalLabel" aria-hidden="true">
    <form>
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deposit_modalLabel">Supprimer cette ligne ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body" id="deposit_modalBody"></div>
                <div class="modal-footer d-flex justify-content-between" id="deposit_modalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="deposit_modalSave">
                        <i class="fa-solid fa-check"></i> {{ __('ui.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@once
    @push('js')
        <script>

            const deposits = {
                container: function () {
                    return $('#deposits');
                },
                rows: function () {
                    return this.container().find('.rows');
                },
                notice: function () {
                    return this.container().find('.mfw-notice');
                },
                add: function () {
                    $('#add_deposit').off().click(function () {
                        let identifier = 'deposit-' + guid();
                        if (!deposits.rows().length) {
                            deposits.container().prepend('<div class="rows"></div>');
                        }
                        deposits.notice().addClass('d-none');
                        console.log(deposits.rows());
                        deposits.rows().append($('#deposit_template').html());
                        deposits.rows().find('.row').last().addClass(identifier).find('li').attr('data-target', identifier);
                        setDatepicker();
                    });
                },
                modals: function () {

                    let deposit_modal = $('#destroy_deposit_modal');

                    deposit_modal.on('show.bs.modal', function (event) {
                        $('#deposit_modalSave').off().on('click', function () {
                            $('.' + $(event.relatedTarget).attr('data-target')).remove();
                            deposit_modal.modal('hide');
                        });
                    });

                    deposit_modal.on('hide.bs.modal', function () {
                        if (deposits.rows().find('> div').length === 0) {
                            deposits.notice().removeClass('d-none');
                        }
                    });
                },
                init: function () {
                    deposits.add();
                    deposits.modals();
                },
            };

            deposits.init();

        </script>
    @endpush
@endonce
