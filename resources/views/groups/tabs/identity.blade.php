<div class="tab-pane fade show active"
     id="group-tabpane"
     role="tabpanel"
     aria-labelledby="group-tab">
    <div class="row mb-4">
        <div class="col-lg-12 pt-3">
            @php
                $error = $errors->any();
            @endphp
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <x-mfw::input required="true"
                                  name="group[name]"
                                  :label="__('ui.title')"
                                  :value="$error ? old('group.name') : $data->name" />
                </div>
                <div class="col-lg-6 mb-3">
                    <x-mfw::input required="true"
                                  name="group[company]"
                                  class="wg_complete"
                                  :label="__('forms.fields.company_name')"
                                  :value="$error ? old('group.company') : $data->company" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <x-mfw::input name="group[siret]"
                                  label="SIRET"
                                  :value="$error ? old('group.siret') : $data->siret" />
                </div>
                <div class="col-lg-6 mb-3">
                    <x-mfw::input name="group[vat_id]"
                                  class="wg_complete"
                                  label="Numéro de TVA"
                                  :value="$error ? old('group.vat_id') : $data->vat_id" />
                </div>
            </div>
            <div class="col-lg-12 mb-3">
                <x-mfw::textarea :label="__('forms.fields.group_billing_comment')"
                                 name="group[billing_comment]"
                                 :value="$error ? old('group.billing_comment') : $data->billing_comment" />
            </div>

            <div class="row">
                <div class="col mt-4">
                    <h4>Téléphone siège</h4>
                    <x-back.phone-row namespace="group" :phone="$data->phone" />
                </div>
            </div>

            @if(!$data->id)
                <div class="row">
                    <div class="col mt-4">
                        <h4>Adresse principale</h4>
                        <x-mfw::google-places :geo="new \App\Models\GroupAddress"
                                              label="Adresse principale (taper pour obtenir des résultats) *"/>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
