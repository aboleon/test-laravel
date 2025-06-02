@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade" id="texts-tabpane" role="tabpanel" aria-labelledby="texts-tabpane-tab">
    <div class="row pt-3">
        <div class="col-md-6 pe-sm-5">
            <h4>Textes</h4>
            <x-mfw::translatable-tabs datakey="service_texts" :pluck="['description','vat_description']" :fillables="$data->fillables" :model="$data"/>
        </div>
        <div class="col-md-6 ps-sm-5">
            @include('events.manager.sellable.inc.options')
        </div>
    </div>
</div>
