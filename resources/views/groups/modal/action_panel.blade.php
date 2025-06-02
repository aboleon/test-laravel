@php
    $isEdit = false;
    $ids = [];

//    if(isset($account)){
//        $isEdit = true;
//        $ids[] = $account->id;
//    }

@endphp
<div class="modal fade"
     id="modal_actions_panel"
     tabindex="-1"
     aria-labelledby="modal_actions_panel_label"
     aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-form" data-ajax="{{route('ajax')}}">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-end">
                    <h1 class="modal-title fs-5" id="modal_actions_panel_label">Actions</h1>
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
                <div class="modal-body">

                    @if(false === $isEdit)
                        <div class="row mb-3">
                            <div class="col-6">

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="mode"
                                           value="selection"
                                           id="target_selection"
                                           checked>
                                    <label class="form-check-label" for="target_selection">
                                        Appliquer à la sélection
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="mode"
                                           value="all"
                                           id="target_all">
                                    <label class="form-check-label" for="target_all">
                                        Appliquer à tous les résultats
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endif
                    <div class="action-container mb-3">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="action"
                                           value="associateGroupsToEvent"
                                           id="flexRadioDefault1"
                                           checked>
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Affecter à un événement
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <select class="form-select mb-3"
                                        name="associateGroupsToEvent[event_id]">
                                    <option selected value="">Choix événement</option>
                                    @foreach(App\Accessors\EventAccessor::eventsArray() as $id => $label)
                                        <option value="{{$id}}">{{$label}}</option>
                                    @endforeach
                                </select>
                            </div>
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


@push('js')
    <script>

        $(document).ready(function () {

            $('#modal_actions_panel').handleMultipleSelectionModal({
                ids: {{ Js::from($ids) }},
                noSelectionMessage: 'Veuillez sélectionner au moins un groupe',
                prevalidationHandler: function (oFormData) {
                    switch (oFormData.action) {
                        case 'associateGroupsToEvent':
                            if ('' === oFormData['associateGroupsToEvent[event_id]']) {
                                return 'Veuillez sélectionner un événement';
                            }
                            break;
                    }
                },
            });
        });
    </script>
@endpush

@pushonce('js')
    <script src="{{asset('js/handleMultipleSelectionModal.jquery.js')}}"></script>
@endpushonce
