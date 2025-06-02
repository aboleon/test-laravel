<div class="row mb-5">
    <div class="col-12">
        <h4>Prestation liée</h4>
        <small class="d-block" style="font-size: 12px;margin: -15px 0 20px">* par nuit et par personne</small>
    </div>
    <div class="col-12">
        <x-mfw::translatable-tabs id="service"
                                  :fillables="(new \App\Models\EventManager\Accommodation\Service())->fillables"
                                  :model="$accommodation->service ?: new \App\Models\EventManager\Accommodation\Service()"
                                  datakey="service"/>
    </div>
    <div class="col-md-6">

        <b class="d-block text-dark mb-2">Types de participation</b>
        <div id="service_participation_types">
            <x-participation-types name="service[participation_types]"
                                   :subset="$event->participations->pluck('id')->toArray()"
                                   :affected="collect($error ? old('service.participation_types') : explode(',',$accommodation->service?->participation_types))"
                                   :filter="true" />
        </div>
    </div>
    <div class="col-md-6">
        <x-mfw::input type="number"
                      name="service[price]"
                      :value="$error ? old('service.price') : $accommodation->service?->price"
                      label="Montant € TTC"
                      :params="['min'=>0,'step'=>'0.01']"/>
        <br>
        <x-mfw::select name="service[vat_id]"
                       :values="MetaFramework\Accessors\VatAccessor::readableArrayList()"
                       :affected="$error ? old('service.vat_id') : ($accommodation->service?->vat_id ?: \MetaFramework\Accessors\VatAccessor::defaultRate()?->id)"
                       :label="__('mfw-sellable.vat.label')" :nullable="false"/>
    </div>

</div>
