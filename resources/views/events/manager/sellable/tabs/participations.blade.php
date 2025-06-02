@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade" id="ptypes-tabpane" role="tabpanel" aria-labelledby="ptypes-tabpane-tab">
    <div class="row p-3">
        <div class="col-md-6 pe-sm-5 mass_checker">
            <h4>Types de participations</h4>
            <div id="participation_types">
                <x-mfw::checkbox name="all_participations_toggler" class="checker" label="Cocher / décocher tout" value="1"/>
                <x-participation-types :filter="true"
                                       :subset="$event->participations->pluck('id')->toArray()"
                                       name="service_participations"
                                       :affected="$error ? old('service_participations') : $data->participations->pluck('id')"/>
            </div>
        </div>
        <div class="col-md-6 ps-sm-5 mass_checker">
            <h4>Types de professions</h4>
            @if ($professions->entries->isNotEmpty())
                <x-mfw::checkbox name="all_professions_toggler" class="checker" label="Cocher / décocher tout" value="1"/>
                <ul class="list-unstyled meta-checkable">
                    @foreach($professions->entries as $item)
                        <x-dico-form-printer tag="li"
                                             :filter="true"
                                             :item="$item"
                                             :subset="$event->professions->pluck('id')->toArray()"
                                             :affected="$error ? collect(old('service_professions')) : $data->professions->pluck('id')"
                                             form-tag="service_professions[]"/>
                    @endforeach
                </ul>
            @else
                <x-mfw::notice message="Aucune profession n'est saisie"/>
            @endif
        </div>
    </div>
</div>

