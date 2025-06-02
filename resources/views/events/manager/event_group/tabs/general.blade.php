@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade"
     id="general-tabpane"
     role="tabpanel"
     aria-labelledby="general-tabpane-tab">
    <div class="col">

        <form method="post" action="{{route('panel.manager.event.event_group.update', [
        'event' => $event,
        'event_group' => $eventGroup,
        ])}}">
            @csrf
            @method('PUT')

            <div class="wg-card">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-mfw::radio
                                    name="is_exhibitor"
                                    :values="[1 => 'Oui', 0 => 'Non']"
                                    label="Le groupe est exposant"
                                    :default="(int)$eventGroup->is_attending"
                                    :affected="$error ? old('is_exhibitor') : ($eventGroup->id ? (int)$eventGroup->is_exhibitor : 0)" />
                        </div>
                        <div class="mb-3">
                            <x-mfw::input name="password"
                                          :value="$error ? old('password') : $eventGroup->password"
                                          label="Mot de passe exposant" />

                        </div>
                        <div class="mb-3">
                            <x-mfw::input name="nb_free_badges"
                                          type="number"
                                          :value="$error ? old('password') : $eventGroup->nb_free_badges"
                                          label="Nb badges gratuits" />
                        </div>
                        <div class="mb-3">
                            <x-mfw::textarea name="comment"
                                             label="Commentaire"
                                             :value="$error ? old('comment') : $eventGroup->comment" />
                        </div>
                        <div class="mb-3">
                            <x-mfw::textarea name="event_comment"
                                             label="Commentaire évènement"
                                             :value="$error ? old('event_comment') : $eventGroup->event_comment" />

                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="my-3">
                            <x-mfw::input name="free_text_1"
                                          :value="$error ? old('free_text_1') : $eventGroup->free_text_1"
                                          label="Champ libre 1" />

                        </div>
                        <div class="mb-3">

                            <x-mfw::input name="free_text_2"
                                          :value="$error ? old('free_text_2') : $eventGroup->free_text_2"
                                          label="Champ libre 2" />

                        </div>
                        <div class="mb-3">

                            <x-mfw::input name="free_text_3"
                                          :value="$error ? old('free_text_3') : $eventGroup->free_text_3"
                                          label="Champ libre 3" />
                        </div>
                        <div class="mb-3">


                            <x-mfw::input name="free_text_4"
                                          :value="$error ? old('free_text_4') : $eventGroup->free_text_4"
                                          label="Champ libre 4" />
                        </div>
                    </div>
                </div>


                <button type="submit" class="btn btn-sm btn-warning mt-3">
                    <i class="fa-solid fa-check"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
