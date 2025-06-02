@php use App\Accessors\EventContactAccessor; @endphp
<div class="modal fade"
     id="new-inscription-modal"
     tabindex="-1"
     aria-hidden="true">
    <form class="modal-dialog" data-ajax="{{route('ajax')}}">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Nouvelle Inscription</h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="modal-body" x-data="{
                response: true,
                invitation_quantity_enabled: {{$invitationQuantityEnabled?'true':'false'}}
            }"
                 @config_invitation_quantity_changed.window="invitation_quantity_enabled = $event.detail.value"
            >

                <div class="messages"></div>

                <div class="mb-3">
                    @php
                        $participants = EventContactAccessor::selectableByEvent($event);
                    @endphp
                    <label for="new-inscription-participants"
                           class="form-label">Participant</label>
                    <select class="form-select"
                            name="participant_id[]"
                             multiple="multiple"
                            id="new-inscription-participants">
                        {{-- <option selected value="">--- Choisissez ---</option> --}}
                        @foreach($participants as $id => $fullName)
                            <option value="{{$id}}">{{$fullName}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="mb-3 d-flex gap-3">
                    <label for="new-inscription-response" class="form-label">Réponse</label>
                    <div class="form-check">
                        <input class="form-check-input"
                               @change="response = true"
                               type="radio"
                               name="response"
                               value="1"
                               id="new-inscription-response-yes"
                               checked
                        >
                        <label class="form-check-label"
                               for="new-inscription-response-yes">Oui</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                               @change="response = false"
                               type="radio"
                               name="response"
                               value="0"
                               id="new-inscription-response-no"
                        >
                        <label class="form-check-label"
                               for="new-inscription-response-no">Non</label>
                    </div>
                </div>


                @if($invitationQuantityEnabled)
                    <div class="mb-3" x-show="true === response" x-cloak>

                        <label for="new-inscription-quantity" class="form-label">Nombre de
                            personnes</label>
                        <select class="form-select"
                                name="quantity"
                                id="new-inscription-quantity">
                            <option selected value="">--- Choisissez ---</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler
                </button>
                <button type="submit" class="btn-new-inscription-save btn btn-primary">
                    Enregistrer
                    <div style="display: none;"
                         class="spinner-new-inscription spinner-border spinner-border-sm"
                         role="status">
                        <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                    </div>
                </button>
            </div>
        </div>
    </form>
</div>
