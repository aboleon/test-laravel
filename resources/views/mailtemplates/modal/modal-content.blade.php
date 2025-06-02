<div class="modal fade"
     id="modal_send_mail"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-form" data-ajax="{{route('ajax')}}">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex align-items-end">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Actions</h1>
                    <div class="spinner-element ms-3" style="display: none;">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body modal-body-mail">
                    <input type="hidden" name="event_id" value="{{$event_id}}" />
                    <input type="hidden" name="contact_ids" value="{{$ids}}">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="action"
                                       value="sendMailTemplateFromModal"
                                       id="sendMailTemplateFromModal"
                                       checked>
                                <label class="form-check-label" for="sendMailTemplateFromModal">
                                    Envoyer un mail
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="action"
                                       value="sendPdf"
                                       id="sendPdf">
                                <label class="form-check-label" for="sendPdf">
                                    Envoyer un PDF
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="action"
                                       value="generatePdf"
                                       id="generatePdf">
                                <label class="form-check-label" for="generatePdf">
                                    Générer un PDF
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-12 mb-2">
                            <x-mfw::select name="mailtemplate_id" label="Courrier" :values="$mailtemplates" :nullable="true" />
                        </div>
                        <div class="col-12 mail_object mb-2">
                            <x-mfw::input name="object_fr" label="Objet FR" value="" />
                        </div>
                        <div class="col-12 mail_object mb-2">
                            <x-mfw::input name="object_en" label="Objet EN" value="" />
                        </div>
                        <div class="col-12 mail_content mb-2" style="display:none;">
                            <x-mfw::textarea class="extended" name="content_fr" label="Message FR" value="" />
                        </div>
                        <div class="col-12 mail_content mb-2" style="display:none;">
                            <x-mfw::textarea class="extended" name="content_en" label="Message EN" value="" />
                        </div>
                    </div>
                </div>
                <div class="messages m-3"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" class="btn btn-primary submit-btn">Exécuter</button>
                </div>
            </div>
        </form>
    </div>
</div>
