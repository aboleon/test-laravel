@php
    $prices = $data->prices;
        $error = $errors->any();
        if ($errors->any()) {
            if (old('service_price')) {
                $d = old('service_price');

            $prices = collect();
                for($i=0;$i<count($d['price']);$i++) {

                    try {
                        $ends_at = \Carbon\Carbon::createFromFormat('d/m/Y', $d['ends'][$i])->format('d/m/Y');
                    } catch (Throwable) {
                        $ends_at = null;
                    }
                    $prices->push(new \App\Models\EventManager\Sellable\Price([
                    'price' => $d['price'][$i],
                    'ends' => $ends_at
                    ])
                    );
                }
            }
        }
@endphp
<h4>Prix</h4>
<div class="row mb-4">
    <div class="col-sm-4">
        <x-mfw::select name="service.vat_id"
                       :values="MetaFramework\Accessors\VatAccessor::readableArrayList()"
                       :affected="$error ? old('service.vat_id') : ($data->vat_id ?: \MetaFramework\Accessors\VatAccessor::defaultRate()?->id)"
                       :label="__('mfw-sellable.vat.label')" :nullable="false"/>
    </div>
</div>
<div id="time-binded-prices" class="test">
    @foreach($prices as $price)
        <x-time-binded-price-row :price="$price" prefix="service_price" callback="deleteServicePriceRow" :sellable="$data"/>
    @endforeach
</div>
<button class="btn btn-sm btn-success mt-3" id="add-time-binded-price" type="button">
    <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
</button>

<x-mfw::validation-error field="service_price.price"/>

<div id="sellable_service_price_messages" data-ajax="{{ route('ajax') }}"></div>
<template id="time-binded-price-row">
    <x-time-binded-price-row :price="new \App\Models\EventManager\Sellable\Price()" prefix="service_price"
                             callback="deleteServicePriceRow" :sellable="$data"/>
</template>
@push('callbacks')
    <script>
        function ajaxPostDeletePrice(result) {
            $(result.input.identifier).remove();
        }
    </script>
@endpush
@push('js')
    <script>
        $(function () {
            if (!$('#time-binded-prices > div').length) {
                $('#add-time-binded-price').click();
            }
        });

        function deleteServicePriceRow() {
            $('.delete_time_binded_price').off().on('click', function () {

                $('.messages').html('');
                let id = $(this).attr('data-model-id'),
                    identifier = '.time-binded-price-row[data-identifier=' + $(this).attr('data-identifier') + ']';
                $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                console.log(id, identifier, (id.length < 1 || isNaN(id)));
                if (id.length < 1 || isNaN(id)) {
                    $(identifier).remove();
                } else {
                    ajax('action=removeTimeBindedPriceRow&id=' + Number(id) + '&identifier=' + identifier, $('#sellable_service_price_messages'));
                }
            });
        }
    </script>
@endpush
