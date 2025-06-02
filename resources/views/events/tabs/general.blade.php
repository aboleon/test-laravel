@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade show active"
     id="general-tabpane"
     role="tabpanel"
     aria-labelledby="general-tabpane-tab">
    <div class="row pt-4">
        <div class="col-xxl-6">
            <x-mfw::translatable-tabs :fillables="$data->fillables['general']"
                                      id="general"
                                      datakey="event[texts]"
                                      :model="$texts"/>

            <div class="mfw-line-separator mb-4"></div>

            <div class="row">
                <div class="col-xl-6">
                    <strong class="d-block mb-3">Affichage des drapeaux</strong>
                    <div class="d-flex">
                        @foreach(config('mfw.translatable.locales') as $locale)
                            <div class="me-4">
                                <x-mfw::checkbox :switch="true"
                                                 name="event[config][flags][]"
                                                 :value="$locale"
                                                 :label="__('lang.'.$locale.'.label')"
                                                 :affected="collect($error ? old('event.config.flags') : ($data->id ? $data->flags : ($locale == app()->getLocale() ? [$locale] : [])))"/>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-xl-6 d-flex align-items-center justify-content-end">
                    <x-mfw::checkbox :switch="true"
                                     name="event[config][published]"
                                     value="1"
                                     label="En ligne"
                                     :affected="collect($error ? old('event.config.published') : ($data->id ? $data->published : [0]))"/>
                </div>
                <div class="mfw-line-separator mt-4 mb-4"></div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::datepicker label="Début"
                                       name="event[config][starts]"
                                       required="true"
                                       :value="$error ? old('event.config.starts') : $data->starts"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::datepicker label="Fin"
                                       name="event[config][ends]"
                                       required="true"
                                       :value="$error ? old('event.config.ends') : $data->ends"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::datepicker label="Date limite des inscriptions"
                                       name="event[config][subs_ends]"
                                       required="true"
                                       :value="$error ? old('event.config.subs_ends') : $data->subs_ends"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="w-100 me-3">
                            <x-mfw::select name="event[config][place_id]"
                                           label="Lieu *"
                                           :values="$places"
                                           :affected="$error ? old('event.config.place_id') : $data->place_id"/>
                        </div>
                        <a href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#mfwDynamicModal"
                           data-modal-shown="rebindDictionary"
                           data-modal-content-url="{{ route('panel.modal', ['requested' => 'createPlace']) }}"
                           class="fs-4 add-dynamic"><i class="fa-solid fa-circle-plus"></i></a>
                    </div>
                </div>
            </div>

            <div class="mfw-line-separator mt-4 mb-4"></div>
            <x-mfw::translatable-tabs :fillables="$data->fillables['event']" id="event_texts" datakey="event[texts]" :model="$texts"/>
        </div>
        <div class="col-xxl-6 ps-xxl-5 pt-2">
            <div class="row pt-5">

                <div class="col-xl-6 mb-3 mfw-holder position-relative">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="w-100 me-3">
                            <x-selectable-dictionnary key="event_family"
                                                      name="event[config][event_main_id]"
                                                      :affected="$error ? old('event.config.event_main_id') : $data->event_main_id"/>
                        </div>
                        <span class="fs-4 add-dynamic dict-dynamic" data-dict="event_family"><i
                                class="fa-solid fa-circle-plus"></i></span>
                    </div>
                </div>

                <div class="col-xl-6 mb-3 mfw-holder position-relative">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="w-100 me-3">
                            <x-selectable-dictionnary key="event_type"
                                                      name="event[config][event_type_id]"
                                                      required="true"
                                                      :affected="$error ? old('event.config.event_type_id') : $data->event_type_id"/>
                        </div>
                        <span class="fs-4 add-dynamic dict-dynamic"
                              data-dict="event_type"><i class="fa-solid fa-circle-plus"></i></span>
                    </div>
                </div>


                <div class="col-xl-6 mb-3">
                    <x-mfw::select name="event[config][bank_account_id]"
                                   :label="trans_choice('bank.label',1) . ' *'"
                                   :values="\App\Accessors\BankAccounts::selectables()"
                                   :affected="$error ? old('event.config.bank_account_id') : $data->bank_account_id"/>
                </div>

                <div class="col-xl-6 mb-3">
                    <x-mfw::input name="event[config][code]" label="Code" :value="$error ? old('event.config.code') : $data->code" required="true" />
                </div>

                <div class="mfw-line-separator mt-4 mb-4"></div>

                <div class="col-xl-6 mb-3">
                    <x-mfw::select name="event[config][admin_id]"
                                   label="Admin évènement (CDP) *"
                                   :values="$admin_users"
                                   :affected="$error ? old('event.config.admin_id') : $data->admin_id"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::select name="event[config][admin_subs_id]"
                                   label="Admin inscription *"
                                   :values="$admin_users"
                                   :affected="$error ? old('event.config.admin_subs_id') : $data->admin_subs_id"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::input name="event[config][bank_card_code]"
                                  :value="$error ? old('event.config.bank_card_code') : $data->bank_card_code"
                                  label="Code CB"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::input type="number"
                                  :params="['min'=>1]"
                                  name="event[config][reminder_unpaid_accommodation]"
                                  :value="$error ? old('event.config.reminder_unpaid_accommodation') : $data->reminder_unpaid_accommodation"
                                  label="Relance hébergement non soldé (en jours)"/>
                </div>
                <div class="col-12 mb-3">
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6">
                            <x-mfw::checkbox :switch="true"
                                             name="event[config][has_abstract]"
                                             value="1"
                                             label="Abstract"
                                             :affected="collect($error ? old('event.config.has_abstract') : ($data->id ? $data->has_abstract : [1]))"/>
                        </div>
                    </div>
                    <div class="mfw-line-separator mt-4 mb-4"></div>
                    <div class="mt-4 mb-4">
                        <x-mfw::checkbox :switch="true"
                                         name="event[config][has_external_accommodation]"
                                         value="1"
                                         class="mb-4"
                                         label="Hébergement géré en externe"
                                         :affected="collect($error ? old('event.config.has_external_accommodation') : $data->has_external_accommodation)"/>
                        <x-mfw::translatable-tabs :fillables="$data->fillables['accommodation']" id="accommodation_texts" datakey="event[texts]" :model="$texts"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
