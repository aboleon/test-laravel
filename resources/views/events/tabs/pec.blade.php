<div class="tab-pane fade" id="pec-tabpane" role="tabpanel" aria-labelledby="pec-tabpane-tab">
    <div class="row pt-4">
        <div class="col-12 mb-3">
            <x-mfw::checkbox :switch="true" name="event[pec][is_active]" value="1" label="Activer PEC" :affected="collect($error ? old('event.pec.is_active') : ($data->id ? $data->pec?->is_active : 1))"/>
        </div>
        <div class="col-xl-6 mb-3">
            <x-mfw::select name="event[pec][admin_id]" label="Admin PEC" :values="$admin_users" :affected="$error ? old('event.pec.admin_id') : $data->pec?->admin_id"/>
        </div>
        <div class="col-xl-6 mb-3">
            <x-mfw::select name="event[pec][grant_admin_id]" label="Admin GRANT" :values="$admin_users" :affected="$error ? old('event.pec.grant_admin_id') : $data->pec?->grant_admin_id"/>
        </div>
        <div class="col-xl-6 mb-3">
            <x-mfw::input type="number" :params="['min'=>1]" name="event[pec][processing_fees]" :value="$error ? old('event.pec.processing_fees') : $data->pec?->processing_fees" label="Frais de dossier PEC €/TTC"/>
        </div>
        <div class="col-xl-6 mb-3">
            <x-mfw::select name="event[pec][processing_fees_vat_id]" :values="\MetaFramework\Accessors\VatAccessor::selectables()" :affected="$error ? old('event.pec.processing_fees_vat_id') : $data->pec?->processing_fees_vat_id" label="Taux TVA Frais de dossier PEC"/>
        </div>
        <div class="col-xl-6 mb-3">
            <x-mfw::input type="number" :params="['min'=>1]" name="event[pec][waiver_fees]" :value="$error ? old('event.pec.waiver_fees') : $data->pec?->waiver_fees" label="Montant caution €/TTC"/>
        </div>
        <div class="col-xl-6 mb-3">
            <x-mfw::select name="event[pec][waiver_fees_vat_id]" :values="\MetaFramework\Accessors\VatAccessor::selectables()" :affected="$error ? old('event.pec.waiver_fees_vat_id') : $data->pec?->waiver_fees_vat_id" label="Taux de TVA facturation caution"/>
        </div>

        <div class="col-xl-6 mt-4">
            <h4>Types de participation éligibles</h4>
            <div id="pec_participations" class="list-unstyled">
                <x-participation-types :filter="true" :subset="$data->participations->pluck('id')->toArray()" name="pec_participations" :affected="$error ? old('pec_participations') : $data->pecParticipations->pluck('id')" :all="true"/>
            </div>
        </div>
        <div class="col-xl-6 mt-4">
            <h4>Domaines éligibles</h4>
            <ul id="pec_domains" class="list-unstyled">
                @if($domains->entries->isNotEmpty())
                    @foreach($domains->entries as $assigned)
                        <x-dico-form-printer tag="li" :item="$assigned" :filter="true" :subset="$data->domains->pluck('id')->toArray()" :affected="$data->pecDomains->pluck('id')" form-tag="pec_domains[]"/>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>
