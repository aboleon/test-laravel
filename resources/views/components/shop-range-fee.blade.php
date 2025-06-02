<div class="row mb-3 align-items-end shop-range-{{ $range->id ?? 0 }}">
    <div class="col-md-5">
        <x-mfw::input type="number" :value="$range->port ?: 0" :param="['min'=>0]" name="shop_range[port][]" label="€ / TTC"/>
    </div>
    <div class="col-md-5">
        <x-mfw::input type="number" :value="$range->order_up_to ?: 0" :param="['min'=>0]" name="shop_range[order_up_to][]" label="jusqu'à € / TTC de commande"/>
    </div>
    <div class="col-md-2">
        <ul class="mfw-actions mb-2">
            <x-mfw::delete-modal-link reference="shop_range_modal" title="Supprimer la tranche ?" :params="['data-target' => 'shop-range-'.($range->id ?? 0)]"/>
        </ul>
    </div>
</div>
