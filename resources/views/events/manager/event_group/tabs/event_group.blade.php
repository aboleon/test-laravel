<div class="tab-pane fade"
     id="the-event_group-tabpane"
     role="tabpanel"
     aria-labelledby="the-event_group-tabpane-tab"
     data-ajax="{{route("ajax")}}"
>

    <div class="messages"></div>
    <div class="mt-5 event-group-autocomplete-container">
        <div class="row align-items-center">
            <div class="col-4">
                <livewire:common.autocomplete
                    class="form-control form-select"
                    model-class="App\Accessors\EventContactAccessor"
                    :show-results-on-click="true"
                    get-items-method="getSearchResultsByEventId"
                    :get-items-args="[$event->id, 'input', [
                            'pluck' => true,
                            'exclude_group' => $eventGroup->id
                        ]]"
                    initial-value=""
                />
            </div>
            <div class="col-4">
                <button class="btn-add btn btn-sm btn-primary small">Ajouter ce participant à ce
                    groupe
                    <div class="event-group-attach-user-spinner spinner-border spinner-border-sm"
                         role="status"
                         style="display: none">
                        <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                    </div>
                </button>
            </div>
            <div class="col-4">
                <div class="mb-3">
                    <label for="main_contact_id" class="form-label">
                        Contact principal du groupe
                    </label>
                    <div class="input-group mb-3">
                        @php
                            $groupMembers = \App\DataTables\View\EventContactView::where('event_id', $event->id)
                            ->selectRaw('user_id, concat_ws(" ", UPPER(last_name), first_name) as name')
                            ->orderBy('last_name')
                            ->pluck('name','user_id')
                            ->toArray();
                        @endphp

                        <x-mfw::select name="main_contact_id"
                                       :affected="$eventGroup->main_contact_id"
                                       :values="$groupMembers"
                        />

                        <button class="action-make-main-contact btn btn-primary btn-sm"
                                type="button">
                            Enregistrer
                            <div class="spinner-border spinner-border-sm"
                                 role="status"
                                 style="display: none;">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>
                        </button>
                        <button class="action-send-group-manager-confirm-notif btn btn-warning btn-sm"
                                data-bs-toggle="tooltip"
                                data-bs-title="Envoyer le mail de confirmation au contact principal du groupe"
                                type="button">
                            <i class="bi bi-envelope"></i>
                            <div class="spinner-border spinner-border-sm"
                                 role="status"
                                 style="display: none;">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="wg-card event_group_dashboard mt-4">
        <div class="text-center">
            <div class="dt-spinner spinner-border spinner-border-sm"
                 role="status"
                 style="display: none;">
                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
            </div>
        </div>

        <div class="datatable-not-clickable">
            {!! $eventGroupContactDatatable->table()  !!}
        </div>
        @include('lib.datatable')
        @push('js')
            {{ $eventGroupContactDatatable->scripts() }}


            <script>
                $(document).ready(function () {
                    const jAjaxContext = $('#the-event_group-tabpane');

                    //----------------------------------------
                    // autocomplete
                    //----------------------------------------
                    const jContainer = $('.event-group-autocomplete-container');
                    const jSpinner = jContainer.find('.event-group-attach-user-spinner');
                    const jButton = jContainer.find('.btn-add');
                    const jBtnMakeMainContact = jContainer.find('.action-make-main-contact');
                    const jBtnSendGroupManagerConfirmNotif = jContainer.find('.action-send-group-manager-confirm-notif');
                    jButton.on('click', function () {
                        let jInput = jContainer.find('input[type=hidden]');
                        let eventContactId = jInput.val();
                        if (eventContactId) {
                            ajax('action=associateEventContactToEventGroup&event_contact_id=' + eventContactId +
                                '&event_group_id={{$eventGroup->id}}', jAjaxContext, {
                                    spinner: jSpinner,
                                    successHandler: function (result) {
                                        $('.dt').DataTable().ajax.reload();
                                        $('<option value="' + result.user_id + '">' + result.user_name + '</option>').insertAfter($('#main_contact_id').find('option').first());
                                    },
                                },
                            );
                        }
                        return false;
                    });

                    jBtnMakeMainContact.on('click', function () {
                        let action = 'action=makeMainContactOfTheEventGroup';
                        let jSelect = jAjaxContext.find('select[name=main_contact_id]');
                        let mainContactId = jSelect.val();
                        ajax(action + '&event_group_id={{$eventGroup->id}}&user_id=' + mainContactId, jAjaxContext, {
                            spinner: jBtnMakeMainContact.find('.spinner-border'),
                            successHandler: function () {
                                $('.dt').DataTable().ajax.reload();
                                return true;
                            },
                        });
                        return false;
                    });

                    jBtnSendGroupManagerConfirmNotif.on('click', function () {
                        let jSelect = jAjaxContext.find('select[name=main_contact_id]');
                        let mainContactId = jSelect.val();


                        let action = 'action=sendConnexionMailToEventGroupMainContact';
                        action += '&user_id=' + mainContactId;
                        action += '&event_id={{$event->id}}';
                        action += '&group_id={{$eventGroup->group_id}}';


                        ajax(action, jAjaxContext, {
                            spinner: jBtnSendGroupManagerConfirmNotif.find('.spinner-border'),
                        });
                        return false;
                    });
                });

            </script>

        @endpush
        @push("css")
            <style>
                .event_group_dashboard .dataTable {
                    width: 100% !important;
                }
            </style>
        @endpush
    </div>

</div>




