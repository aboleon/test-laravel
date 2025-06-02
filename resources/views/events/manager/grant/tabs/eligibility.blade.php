@php
    use App\Accessors\Dictionnaries;
    $error = $errors->any();
@endphp
<div class="tab-pane fade"
     id="eligibility-tabpane"
     role="tabpanel"
     aria-labelledby="eligibility-tabpane-tab">
    @push('css')
        <style>
            #eligibility-tabpane input[type=number] {
                padding: .375rem !important;
            }
        </style>
    @endpush

    <h4>Ages éligibles</h4>
    <div class="row mb-4">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <x-mfw::number min="0"
                                   name="grant.age_eligible_min"
                                   :value="$error ? old('grant.age_eligible_min') : $data->age_eligible_min"
                                   label="Age minimum"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::number min="0"
                                   name="grant.age_eligible_max"
                                   :value="$error ? old('grant.age_eligible_max') : $data->age_eligible_max"
                                   label="Age maximum"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 pb-4">
            <h4>Domaines éligibles</h4>
            @if ($event->pecDomains->isEmpty())

                <x-mfw::alert message="Aucun domaine n'a été configuré dans la gestion de l'évènement"/>

            @else
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Actif</th>
                        <th>Domaine</th>
                        <th>Nombre PEC</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($event->pecDomains as $domain)
                        @php
                            $selected = $data->domains->filter(fn($item) => $item->domain_id == $domain->id)->first();
                        @endphp
                        <tr>
                            <td>
                                <x-mfw::checkbox name="grant_domains.{{ $domain->id }}.active"
                                                 value="1"
                                                 :affected="$error ? (bool)old('grant_domains.'.$domain->id.'.active') : (bool)$selected?->active"/>
                            </td>
                            <td>{{ $domain->name }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <x-mfw::number name="grant_domains.{{ $domain->id }}.pax"
                                                   :value="$error ? old('grant_domains.'.$domain->id.'.pax') : $selected?->pax"/>
                                    <x-back.pec-remover-button/>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="col-sm-6 pb-4">
            <h4>Types de participation éligibles</h4>
            @if ($event->pecParticipations->isEmpty())
                <x-mfw::alert message="Aucun type de participation n'a été configuré dans la gestion de l'évènement"/>
            @else
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Actif</th>
                        <th>Domaine</th>
                        <th>Nombre PEC</th>
                    </tr>
                    </thead>
                    <tbody id="grand_binded_participations">
                    @foreach($event->pecParticipations as $participation)
                        @php
                            $subitem = $data->participationTypes->where('participation_id',$participation->id)->first();
                        @endphp
                        <tr>
                            <td>
                                <x-mfw::checkbox name="grant_participation_types.active."
                                                 value="1"
                                                 :affected="$error ? old('grant_participation_types.is_active.'.$loop->index) == 1 : (bool)$subitem?->active"/>
                            </td>
                            <td>{{ $participation->name }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <x-mfw::number class="me-2"
                                                   name="grant_participation_types.pax."
                                                   :value="$error ? old('grant_participation_types.pax.'.$loop->index) : $subitem?->pax"/>
                                    <x-back.pec-remover-button/>
                                    <input type="hidden"
                                           name="grant_participation_types[id][]"
                                           value="{{ $subitem?->id }}">
                                    <input type="hidden"
                                           name="grant_participation_types[participation_id][]"
                                           value="{{ $participation->id }}">
                                    <input type="hidden"
                                           class="active"
                                           name="grant_participation_types[is_active][]"
                                           value="{{ $error ? (int)old('grant_participation_types.is_active.'.$loop->index) == 1 : (bool)$subitem?->active }}">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="col-sm-6 pb-4">
            <h4>Professions éligibles</h4>
            @if ($event->professions->isEmpty())
                <x-mfw::alert message="Aucune profession n'a été configurée dans la gesion de l'évènement"/>
            @else
                @php
                    $eventProfessionIds = $event->professions->pluck('id')->toArray();
                    $allProfessions = Dictionnaries::selectValues("professions");
                    $affected_professions = $data->professions->pluck('profession_id');
                @endphp
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Actif</th>
                        <th>Domaine</th>
                        <th>Nombre PEC</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if($eventProfessionIds)

                        @foreach($allProfessions as $parentId => $parentInfo)
                            @php
                                $disabled = false;
                                $has_selected_items = array_intersect($eventProfessionIds, array_keys($parentInfo['values']));
                                if(!$has_selected_items) {
                                $disabled = true;
                                }
                                    $selectedParent = $data->professions->filter(fn($item) => $item->profession_id == $parentId)->first();
                            @endphp
                            @if (!$disabled)
                                <tr>
                                    <td class="align-middle">

                                        <x-mfw::checkbox name="grant_profession.{{ $parentId }}.active"
                                                         class="master_profession"
                                                         :value="$parentId"
                                                         :affected="$error ? (bool)old('grant_profession.'.$parentId.'.active') : $selectedParent?->active"/>
                                    </td>
                                    <td>
                                        <b>{{$parentInfo['name']}}</b>
                                    </td>
                                    <td></td>
                                </tr>
                            @endif

                            @if(!$disabled && $parentInfo['values'])
                                @php
                                    $eligibles = array_intersect_key($parentInfo['values'], array_flip($eventProfessionIds));
                                @endphp
                                @foreach( $eligibles as $professionId => $professionName)
                                    @php
                                        $selected = $data->professions->filter(fn($item) => $item->profession_id == $professionId)->first();
                                    @endphp
                                    <tr class="sub">
                                        <td>
                                            <x-mfw::checkbox name="grant_profession.{{ $professionId }}.active"
                                                             value="1"
                                                             class="sub_profession_{{ $parentId }}"
                                                             :affected="$error ? (bool)old('grant_profession.'.$professionId.'.active') : $selected?->active"/>
                                        </td>
                                        <td class="ps-5">
                                            {{$professionName}}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <x-mfw::number name="grant_profession.{{$professionId}}.pax"
                                                               :value="$error ? old('grant_profession.'.$professionId.'.pax') : $selected?->pax"/>
                                                <x-back.pec-remover-button/>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach

                    @else
                        <tr>
                            <td colspan="3">Aucune profession sélectionnée dans la configuration de l'évènement</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif

        </div>
        <div class="col-sm-6">
            <h4>Pays et villes éligibles</h4>
            @include('events.manager.grant.grant-binded-location')
        </div>

        <div class="mfw-line-separator mt-5 mb-5"></div>

        <div class="col-12">
            <h4>Établissements éligibles</h4>
            @include('events.manager.grant.establishments-widget')
        </div>

    </div>

</div>

@push("js")
    <script>
        $('#grand_binded_participations :checkbox').click(function () {
            $(this).closest('tr').find('input.active').val($(this).is(':checked') ? 1 : 0);
        });

        $(document).ready(function () {
            const jContext = $('#eligibility-tabpane');
            jContext.on('click', function (e) {
                let jTarget = $(e.target);
                if (jTarget.hasClass('action-remove-pec-number')) {
                    jTarget.closest('td').find('input').val('');
                }
            });
        });

        $('.master_profession :checkbox').click(function () {
            $('.sub_profession_' + $(this).val() + ' :checkbox').prop('checked', $(this).is(':checked'));
        });

    </script>
@endpush
