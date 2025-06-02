<div class="row mb-3 align-items-end deposit-{{ $id }}">
    <div class="col-md-5">
        <x-mfw::input type="number" :value="$deposit->amount ?: 0" :param="['min'=>1, 'step'=>'any']" name="deposit[amount][]" label="€/HT"/>
    </div>
    <div class="col-md-5">
        <x-mfw::datepicker label="Date" name="deposit[paid_at][]" :value="$deposit->paid_at?->format('d/m/Y')" />
    </div>
    <div class="col-md-2">
        <ul class="mfw-actions mb-2">
            <x-mfw::delete-modal-link reference="deposit_modal" title="Supprimer l'acompte ?" :params="['data-target' => 'deposit-'.$id]"/>
        </ul>
    </div>
</div>
