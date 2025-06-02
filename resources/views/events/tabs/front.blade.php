@php
    $frontConfig = $data->id && $data->frontConfig;
@endphp
<div class="tab-pane fade" id="front-tabpane" role="tabpanel" aria-labelledby="front-tabpane-tab">
    <div class="row gx-5">
        <div class="col-md-6">
            <div class="bloc-miscellaneous">
                <h4>Divers</h4>
                <x-mfw::checkbox :switch="true"
                                 name="event[front_config][speaker_pay_room]"
                                 value="1"
                                 label="Les intervenants paient eux-mêmes leur chambre"
                                 :affected="collect($error ? old('event.front_config.speaker_pay_room') : ($frontConfig ? $data->frontConfig->speaker_pay_room : [1]))"/>

            </div>
            <div class="mfw-line-separator mt-4 mb-4"></div>
            <div class="bloc-contact">
                <h4>Page Contact</h4>
                <x-mfw::translatable-tabs :fillables="$data->fillables['contact']"
                                          id="contact_texts"
                                          datakey="event[texts]"
                                          :model="$texts"/>

            </div>
            <div class="mfw-line-separator mt-4 mb-4"></div>
            <div class="bloc-privacy-policy">
                <h4>Politique de confidentialité</h4>
                <x-mfw::translatable-tabs :fillables="$data->fillables['privacy_policy']"
                                          id="privacy_policy_texts"
                                          datakey="event[texts]"
                                          :model="$texts"/>

            </div>
            <div class="mfw-line-separator mt-4 mb-4"></div>
            <div class="bloc-customisation">
                <h4>Front Customisation</h4>


                <x-mfw::select name="event[front_config][menu_font]"
                               class="mb-3"
                               label="Police menu"
                               :values="\App\Accessors\Fonts::getFrontFontsSelectable()"
                               :affected="$error ? old('event.config.menu_font') : ($frontConfig ? $data->frontConfig->menu_font : 'arial')"/>

                <x-mfw::select name="event[front_config][general_font]"
                               class="mb-3"
                               label="Police générale"
                               :values="\App\Accessors\Fonts::getFrontFontsSelectable()"
                               :affected="$error ? old('event.config.general_font') : ($frontConfig ? $data->frontConfig->general_font : 'arial')"/>

                <x-mfw::input class="minicolors mb-3"
                              name="event[front_config][main_color]"
                              :value="$error ? old('event.config.main_color') : ($frontConfig ? $data->frontConfig->main_color : '#000000')"
                              label="Couleur dominante"/>

                <x-mfw::input class="minicolors mb-3"
                              name="event[front_config][secondary_color]"
                              :value="$error ? old('event.config.secondary_color') : ($frontConfig ? $data->frontConfig->secondary_color : '#000000')"
                              label="Couleur secondaire"/>

                <x-mfw::input class="minicolors mb-3"
                              name="event[front_config][text_color]"
                              :value="$error ? old('event.config.text_color') : ($frontConfig ? $data->frontConfig->text_color : '#000000')"
                              label="Couleur texte"/>


            </div>
            <div class="mfw-line-separator mt-4 mb-4"></div>
            <div class="bloc-mailjet">
                <h4>Mailjet</h4>
                <div class="row">
                    <div class="col-6 mb-3">
                        <x-mfw::input name="event[config][mailjet_newsletter_id]"
                                      :value="$error ? old('event.config.mailjet_newsletter_id') : $data->mailjet_newsletter_id"
                                      label="Id Newsletter"/>
                    </div>
                    <div class="col-6 mb-3">
                        <x-mfw::input name="event[config][mailjet_news_id]"
                                      :value="$error ? old('event.config.mailjet_news_id') : $data->mailjet_news_id"
                                      label="Id News"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <x-mfw::translatable-tabs :fillables="$data->fillables['fo']"
                                      id="fo_texts"
                                      datakey="event[texts]"
                                      :model="$texts"/>
            <hr>
            <x-mfw::translatable-tabs :fillables="$data->fillables['second_fo']"
                                        id="second_fo_texts"
                                        datakey="event[texts]"
                                        :model="$texts"/>

        </div>
    </div>
</div>

