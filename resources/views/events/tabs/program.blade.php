<div class="tab-pane fade"
     id="program-tabpane"
     role="tabpanel"
     aria-labelledby="program-tabpane-tab">

    <div class="pt-4">


        <?php if ($data->id): ?>
        <div class="row mb-3">
            <div class="col-12 mb-3">
                <x-mfw::checkbox :switch="true"
                                 name="event[config][ask_video_authorization]"
                                 value="1"
                                 label="Demander autorisation de diffusion vidéo"
                                 :affected="collect($error ? old('event.config.ask_video_authorization') : ($data->id ? $data->ask_video_authorization : [0]))" />
            </div>
            <div class="col-12" id="event_config__has_program">
                <x-mfw::checkbox :switch="true"
                                 name="event[config][has_program]"
                                 value="1"
                                 label="Activer le programme"
                                 :affected="collect($error ? old('event.config.has_program') : ($data->id ? $data->has_program : [0]))" />
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <p>Vous devez d'abord enregistrer la fiche pour pouvoir configurer le programme.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
