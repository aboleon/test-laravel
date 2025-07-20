<div class="tab-pane fade" id="config-tabpane" role="tabpanel" aria-labelledby="config-tabpane-tab">
    <div class="row pt-4">
        <div class="col-xl-7">
            <h4>Types de participation</h4>
            <div class="list-unstyled event_distributor"
                 id="event_participations"
                 data-target="pec_participations,transport_participations">
                <x-participation-types name="event_participations"
                                       :affected="$error ? collect(old('event_participations')) : ($data->participations->pluck('id') ?? collect([]))"
                                       :all="true"
                                       :alltranslations="true"/>
            </div>

            <div class="mfw-line-separator my-5"></div>
            <h4>Types de professions</h4>
            @if ($professions->entries->isNotEmpty())
                <ul class="list-unstyled meta-checkable">
                    @foreach($professions->entries as $item)
                        <x-dico-form-printer tag="li"
                                             :item="$item"
                                             :affected="$data->professions"
                                             form-tag="event_professions[]"
                                             :alltranslations="true"/>
                    @endforeach
                </ul>
            @else
                <x-mfw::notice message="Aucune profession n'est saisie"/>
            @endif

            <div class="mfw-line-separator my-5"></div>
            <h4>Familles de prestations</h4>
            <table class="table table-compact table-hover" id="services">
                <thead class="bg-body-secondary">
                <tr>
                    <th class="px-4 align-top">Famille</th>
                    <th class="align-top">Résas illimitées</th>
                    <th class="align-top">Limitation à date<br><small class="text-secondary">En
                            front uniquement</small></th>
                    <th class="align-top">Nb Max de résas</th>
                    <th class="align-top">Position de la famille en front et sur page de stats BO</th>
                    <th class="align-top">Compte TVA SAGE</th>
                </tr>
                </thead>

                @if ($services->entries->isNotEmpty())
                    @php
                        $affected_services = $data->eventServices->filter(fn($item) => $item->enabled == 1)->mapWithKeys(fn($item) => [
                                            $item->service_id => [$item]]);
                    @endphp
                    @foreach($services->entries as $item)
                        @php
                            $activeEventService = $data->eventServices->where('service_id', $item->id)->first() ?: new \App\Models\EventService();
                        @endphp
                        <tr class="align-middle">
                            <td class="align-middle service-selector">
                                <x-mfw::checkbox name="event_services[{{$item->id}}][service_id]"
                                                 :value="$item->service_id"
                                                 :affected="$activeEventService?->enabled == 1" :label="$item->name"/>
                            </td>
                            <td class="unlimited">
                                <x-mfw::checkbox name="event_services.{{ $item->id }}.unlimited"
                                                 :value="true"
                                                 :switch="true"
                                                 :affected="$error ? old('service.unlimited') : ($affected_services[$item->id]['unlimited'] ?? null)"/>
                            </td>
                            <td>
                                <x-mfw::checkbox name="event_services.{{ $item->id }}.service_date_doesnt_count"
                                                 label="Ne pas prendre en compte la date *"
                                                 value="1"
                                                 :affected="$error ? old('service.service_date_doesnt_count') : ($affected_services[$item->id]['service_date_doesnt_count'] ?? null)"/>
                            </td>
                            <td>
                                <x-mfw::number :name="'event_services.'.$item->id.'.max'"
                                               :step="1"
                                               :min="1"
                                               :params="Arr::get($affected_services, $item->id.'.unlimited', true) ? ['disabled' => 'disabled'] : []"
                                               :value="$affected_services[$item->id]['max'] ?? 1"/>
                            </td>
                            <td>
                                <x-mfw::number :name="'event_services.'.$item->id.'.fo_family_position'"
                                               :step="1"
                                               :min="0"
                                               :value="$affected_services[$item->id]['fo_family_position'] ?? 0"/>
                            </td>
                            <td>
                                <x-mfw::input :params="['maxlength' => 5]"
                                              class="sageinput"
                                              :randomize="true"
                                              :name="'sage.'.\App\Models\EventService::SAGEVAT .'.'. $item->id"
                                              :value="$activeEventService->getSageReferenceValue(\App\Models\EventService::SAGEVAT)"/>
                                <small class="d-block text-end">limité à 5 caractères</small>

                            </td>
                        </tr>

                    @endforeach
                @else
                    <x-mfw::notice message="Aucune famille de prestation n'est saisie"/>
                @endif
            </table>

            <div class="mfw-line-separator my-5"></div>
            <h4>Intervenants</h4>
            <x-mfw::checkbox :switch="true"
                             name="event[config][show_orators_picture]"
                             value="1"
                             label="Afficher photos intervenants en front"
                             :affected="collect($error ? old('event.config.show_orators_picture') : ($data->id ? $data->show_orators_picture : 1))"/>
            <br/>
            @if ($orators)
                <ul class="list-unstyled">
                    @foreach($orators as $orator_key => $item)
                        <li>
                            <x-mfw::checkbox name="event_orators[]"
                                             :value="$orator_key"
                                             :affected="$data->orators"
                                             :label="$item"/>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-mfw::notice message="Aucune catégorie d'intervenants n'est saisie"/>
            @endif
        </div>
        <div class="col-xl-5 ps-xxl-5">

            <h4>Domaines</h4>
            @if ($domains->entries->isNotEmpty())
                <ul class="list-unstyled event_distributor"
                    id="event_domains"
                    data-target="pec_domains">
                    @foreach($domains->entries as $item)
                        <x-dico-form-printer tag="li"
                                             :item="$item"
                                             :affected="$error ? old('event_domains') instanceof \Illuminate\Support\Collection ? old('event_domains') : collect(old('event_domains')) : $data->domains->pluck('id')"
                                             form-tag="event_domains[]"/>
                    @endforeach
                </ul>
            @else
                <x-mfw::notice message="Aucun domaine n'est saisi"/>
            @endif
        </div>
    </div>
</div>

<x-use-minicolors/>
