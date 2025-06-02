@php use App\Accessors\ProgramContainers; @endphp
<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Sessions</span>
        </h2>


        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>
            @if ($data->id)
                <div class="dropdown">
                    <a class="btn btn-sm mx-2 btn-secondary dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Interventions
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item no-indent"
                               href="{{ route('panel.manager.event.program.intervention.create', [
                               "event" => $event,
                               "session" => $data,
                               ]) }}"><i
                                        class="bi bi-plus-circle-fill"></i> Ajouter une intervention</a>
                        </li>
                        <li><a class="dropdown-item no-indent"
                               href="{{ route('panel.manager.event.program.intervention.index', [
                                "event" => $event,
                               "session" => $data,
                                ]) }}"><i
                                        class="bi bi-list"></i> Liste des interventions</a></li>
                    </ul>

                </div>
                <x-back.topbar.separator />
            @endif


            <x-back.topbar.edit-combo
                    :wrap="false"
                    :event="$event"
                    :model="$data"
                    :item-name="fn($m) => 'la session '. $m->name "
                    :index-route="route('panel.manager.event.program.session.index', [
                                'event' => $event,
                            ])"
                    :create-route="route('panel.manager.event.program.session.create', [
                                'event' => $event,
                            ])"
                    :delete-route="route('panel.manager.event.program.session.destroy', [
                                'event' => $event,
                                'session' => $data->id??'-1',
                            ])"
            />


        </div>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <div class="row m-3">
            <div id="program-session-edit-ajax-container"
                 class="col form"
                 data-ajax="{{ route('ajax') }}">


                <x-mfw::validation-banner />
                <x-mfw::validation-errors />
                <x-mfw::response-messages />

                <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
                    @csrf
                    @if ($data->id)
                        @method('PUT')
                    @endif

                    <div class="row pt-3">
                        <div class="col-xl-6 pe-sm-5">
                            <h4>Session</h4>

                            <x-mfw::checkbox :switch="true"
                                             name="session[main][is_online]"
                                             value="1"
                                             label="{{ __('programs.is_online') }}"
                                             :affected="collect($error ? old('session.main.is_online') : ($data->id ? $data->is_online : [0]))" />

                            <div class="mfw-line-separator mt-4 mb-4"></div>

                            <x-mfw::translatable-tabs datakey="session_texts"
                                                      :fillables="$data->fillables"
                                                      :model="$data" />
                        </div>
                        <div class="col-xl-6 mb-3 mfw-holder position-relative">

                            @if(!$data->id)
                                <div class="row mb-3">
                                    <div class="col-6" x-data="{
                                        showFakeInterventionDuration: false
                                    }">
                                        <x-mfw::checkbox :switch="true"
                                                         name="extra[fake_intervention]"
                                                         value="1"
                                                         label="Créer une intervention"
                                                         :params="[
                                                            'x-model' => 'showFakeInterventionDuration',
                                                         ]"
                                                         :affected="collect(old('extra.fake_intervention', 0))" />
                                            <div class="row mb-3 mt-3" x-show="showFakeInterventionDuration" x-cloak>
                                                <div class="form-text mb-3">
                                                    Une intervention sera créée, qui portera le nom <b>Intervention par défaut</b>.
                                                </div>
                                                <x-mfw::input type="number"
                                                              name="extra[fake_intervention_duration]"
                                                              :value="old('extra.fake_intervention_duration', 15)"
                                                              label="Durée en minutes" />
                                            </div>
                                    </div>
                                    <div class="col-6" x-data="{
                                        showCateringDuration: false
                                    }">
                                        <x-mfw::checkbox :switch="true"
                                                         name="extra[catering]"
                                                         value="1"
                                                         label="Créer catering"
                                                         :affected="collect(old('extra.catering', 0))"
                                                         :params="[
                                                            'x-model' => 'showCateringDuration',
                                                         ]"
                                        />
                                        <div class="row mb-3 mt-3" x-show="showCateringDuration" x-cloak>
                                            <div class="form-text mb-3">
                                                Une intervention sera créée, qui portera le même nom que la session.
                                            </div>
                                            <x-mfw::input type="number"
                                                          name="extra[catering_duration]"
                                                          :value="old('extra.catering_duration', 15)"
                                                          label="Durée en minutes" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mfw-line-separator mt-4 mb-4"></div>
                            @endif

                            <div class="row mb-3">
                                <div class="col d-flex align-items-center">
                                    <div>
                                        @php
                                            $selectableDays = \App\Accessors\Programs::getDayRoomsSelectable($event->id);
                                        @endphp
                                        <x-mfw::select name="session[main][event_program_day_room_id]"
                                                       label="Conteneur *"
                                                       :values="$selectableDays"
                                                       :affected="$error ? old('session.main.event_program_day_room_id') : $data->event_program_day_room_id" />
                                        @if(empty($selectableDays))
                                            <p class="text-secondary mt-1">
                                                <i class="bi bi-exclamation-triangle-fill"
                                                   style="color: var(--base-1);font-size: 16px"></i>
                                                Attention vous n'avez aucune date.
                                                Pour ajouter des dates,
                                                <a href="{{ route('panel.events.edit', $event) }}">éditez
                                                    l'évènement</a>,
                                                <br> et activez le switch <b>"Activer le
                                                    programme"</b>,
                                                puis enregistrez.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-center mt-2">
                                        <div class="spinner-border"
                                             role="status"
                                             id="container_spinner"
                                             style="display: none;"
                                        >
                                            <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">

                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="w-100 me-3 row">

                                        <div class="col-md-6">
                                            <x-mfw::select name="session[main][place_id]"
                                                           label="Lieu"
                                                           :values="ProgramContainers::getPlacesSelectable($event->id)"
                                                           :affected="$error ? old('session.main.place_id') : $place_id" />
                                        </div>

                                        <div class="col-md-6">
                                            <div id="choose-place-message" style="display: none"
                                                 class="p-1 position-relative top-50 text-bg-danger"
                                            >
                                                Veuillez choisir un lieu
                                                d'abord
                                            </div>
                                            <div id="choose-place-select-container">
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div class="me-2">
                                                        <x-mfw::select name="session[main][place_room_id]"
                                                                       label="Salle"
                                                                       :defaultselecttext="$data->id ? '':'Veuillez sélectionner un lieu d\'abord'"
                                                                       :values="$place_rooms"
                                                                       :affected="$error ? old('session.main.place_room_id') : $data->place_room_id" />

                                                    </div>
                                                    <div class="spinner-border spinner-border-sm mt-3 me-2"
                                                         role="status"
                                                         id="place_room_spinner"
                                                         style="display: none">
                                                        <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                                                    </div>
                                                    <a href="#"
                                                       id="create-room-for-place"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#mfwDynamicModal"
                                                       data-modal-on-success="appendRoom"
                                                       data-modal-content-url="{{ route('panel.modal', ['requested' => 'createPlaceRoom', 'selectable' => 'session_main__place_room_id', 'place_id' => old('session.main.place_id', $place_id)]) }}"
                                                       class="fs-4 add-dynamic mt-4"><i class="fa-solid fa-circle-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row mb-3">

                                <div class="d-flex justify-content-between align-items-end mfw-holder">
                                    <div class="w-100 me-3">
                                        <x-selectable-dictionnary key="session_types"
                                                                  required="true"
                                                                  name="session[main][session_type_id]"
                                                                  :affected="$error ? old('session.main.session_type_id') : $data->session_type_id" />
                                    </div>
                                    <span class="fs-4 add-dynamic dict-dynamic"
                                          data-dict="session_types"><i
                                                class="fa-solid fa-circle-plus"></i></span>
                                </div>
                            </div>
                            <div class="row mb-4 mfw-holder position-relative">
                                <div class="d-flex justify-content-between align-items-end col-12 col-md-8">
                                    <div class="w-100 me-3">
                                        <x-selectable-dictionnary key="sponsors"
                                                                  :required="false"
                                                                  name="session[main][sponsor_id]"
                                                                  :affected="old('session.main.sponsor_id', $data->sponsor_id)" />
                                    </div>
                                    <span class="fs-4 add-dynamic dict-dynamic"
                                          data-dict="sponsors"><i
                                                class="fa-solid fa-circle-plus"></i></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-start col-12 col-md-4">
                                    <x-mfw::checkbox :switch="true"
                                                     name="session[main][is_visible_in_front]"
                                                     value="1"
                                                     label="{{ __('programs.is_visible_in_front') }}"
                                                     :affected="collect($error ? old('session.main.is_visible_in_front') : ($data->id ? $data->is_visible_in_front : [0]))" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row mb-3 p-0">
                        <label for="intervention_moderators_ids"
                               class="form-label d-flex gap-2 align-items-center">
                            <span>Modérateurs</span>

                            @if($data->id)
                                <a href="{{route('panel.accounts.create', [
                                'session_id' => $data->id,
                            ])}}"
                                   class="text-success fs-5"><i class="bi bi-plus-circle-fill"></i></a>
                            @endif
                        </label>
                        <select name="intervention[moderators][]"
                                id="intervention_moderators_ids"
                                multiple="multiple">
                        </select>

                    </div>
                    <div class="mb-3 d-flex gap-3 flex-wrap justify-content-between"
                         id="moderator-container">
                    </div>
                </form>
            </div>

        </div>
    </div>


    <template id="tpl-moderator-transport-info">
        <div class="card" style="width:48%;">
            <div class="card-header">
                <a href="#" class="moderator-full-name">full name</a>
            </div>
            <div class="card-body sm">
                <table class="table table-sm">
                    <tbody>
                    <tr>
                        <td>Transport</td>
                        <td class="desired-transport-management">Divine</td>
                    </tr>
                    <tr>
                        <td>Statut</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <select name="intervention[moderators_info][status][]"
                                        class="intervention-statuses">
                                    @foreach(\App\Enum\EventProgramParticipantStatus::translations() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Type modérateur</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <select name="intervention[moderators_info][moderator_type_id][]"
                                        class="moderator-types">
                                    @foreach(\App\Accessors\Dictionnaries::selectValues('program_moderator_type') as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>Autorisation de diffusion vidéo</td>
                        <td class="allow-video-distribution">
                            <div class="d-flex align-items-center">
                                <select name="intervention[moderators_info][allow_video_distribution][]">
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr class="departure-info">
                        <td>Aller</td>
                        <td class="departure-text">
                        </td>
                    </tr>
                    <tr class="return-info">
                        <td>Retour</td>
                        <td class="return-text">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>


    @include('accounts.shared.dict_template')
    @push('modals')
        @include('mfw-modals.launcher')
    @endpush


    @push('js')
        <script>

            function appendRoom(r){
                console.log("append room", r);
                $('#session_main__place_room_id').append('<option value="' + r.room_id + '">' + r.room_name + '</option>');
                $('#mfwDynamicModal').modal('hide');

            }

          activateEventManagerLeftMenuItem('sessions');
          let jRoomCreator = $('#create-room-for-place');
          let jPlaceSelectContainer = $('#choose-place-select-container');
          let jPlaceMessage = $('#choose-place-message');
          let placeId = null;

          interact.selectFeedsSelect('#session_main__place_id', '#session_main__place_room_id', '{{ route('ajax') }}', {
            payload: {
              action: 'getRoomsByPlaceId',
            },
            spinner: $('#place_room_spinner'),
            addPlaceholder: true,
            onChange: function(data, jTargetSelect) {
              placeId = data.input.id;
              if (!placeId) {
                jPlaceSelectContainer.hide();
                jPlaceMessage.show();
              } else {
                jPlaceSelectContainer.show();
                jPlaceMessage.hide();
                const url = new URL(jRoomCreator.data('modal-content-url'));
                const params = url.searchParams;
                params.set('place_id', placeId);
                url.search = params.toString();
                jRoomCreator.attr('data-modal-content-url', url.toString()).change();
              }
            },
          });

          $(document).ready(function() {
            const jAjaxContainer = $('#program-session-edit-ajax-container');
            const jPlaceSelect = $('#session_main__place_id');
            const jPlaceRoomSelect = $('#session_main__place_room_id');

            $('#session_main__event_program_day_room_id').change(function() {
              const dayRoomId = $(this).val();
              if (dayRoomId) {
                ajax('action=getPlaceIdRoomIdPlaceRoomsSelectableByEventProgramDayRoomId&event_program_day_room_id=' + dayRoomId, jAjaxContainer, {
                  spinner: $('#container_spinner'),
                  successHandler: function(resp) {
                    jPlaceSelect.val(resp.place_id);
                    jPlaceRoomSelect.find('option:not(:first-child)').remove();
                    Object.entries(resp.rooms).forEach(function([id, name]) {
                      jPlaceRoomSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                    jPlaceRoomSelect.val(resp.room_id);
                    return true;
                  },
                });
              } else {
                jPlaceSelect.val('');
                jPlaceRoomSelect.find('option:not(:first-child)').remove();
              }
            });

            //----------------------------------------
            // moderators
            //----------------------------------------

            const moderators = {!! \Illuminate\Support\Js::from($moderators) !!};
            let alreadySelectedModeratorIds = [];
            moderators.forEach(function(moderator) {
              alreadySelectedModeratorIds.push(moderator.id);
            });

            let jModeratorContainer = $('#moderator-container');

            $('#intervention_moderators_ids').select2AjaxWrapper({
              placeholder: 'Veuillez sélectionner les modérateurs',
              defaultValues: {!! \Illuminate\Support\Js::from(array_column($moderators, 'name', 'id')) !!},
              multiple: true,
              language: 'fr',
              ajax: {
                url: "{{ route('ajax') }}",
                dataType: 'json',
                data: function(params) {
                  return {
                    q: params.term,
                    event_id: {{$event->id}},
                    intervention_id: {{$data?->id ?? "null"}},
                    alreadySelectedModeratorIds: alreadySelectedModeratorIds,
                    action: 'select2InterventionModeratorsInfo',
                  };
                },
              },
            });

            $('#intervention_moderators_ids').on('select2:select', function(e) {
              ModeratorManager.addModeratorByInfo(e.params.data.info);
              alreadySelectedModeratorIds.push(e.params.data.id);
            });

            $('#intervention_moderators_ids').on('select2:unselect', function(e) {
              const deselectedId = e.params.data.id;
              alreadySelectedModeratorIds = alreadySelectedModeratorIds.filter(id => id != deselectedId);
              ModeratorManager.removeModerator(deselectedId);
            });

            const ModeratorManager = {
              template: $('#tpl-moderator-transport-info').html(),
              container: jModeratorContainer,

              addModerator: function(data) {
                let $clonedTemplate = $(this.template);

                let jFullName = $clonedTemplate.find('.moderator-full-name');
                jFullName.text(data.name);
                jFullName.attr('href', "{{route('panel.manager.event.event_contact.edit', [
                    'event' => $event,
                    'event_contact' => "xxx",
                ])}}".replace('xxx', data.id));

                $clonedTemplate.find('.desired-transport-management').text(data.desired_transport_management);

                $clonedTemplate.find('.allow-video-distribution select').val(data.allow_video_distribution);
                $clonedTemplate.find('.intervention-statuses').val(data.intervention_status);
                $clonedTemplate.find('.moderator-types').val(data.moderator_type_id);

                $clonedTemplate.find('.departure-text').text(data.departure_text);
                $clonedTemplate.find('.return-text').text(data.return_text);
                $clonedTemplate.find('.departure-info').toggle(data.show_departure_text);
                $clonedTemplate.find('.return-info').toggle(data.show_return_text);

                $clonedTemplate.attr('data-id', data.id);
                $clonedTemplate.attr('data-event_contact_intervention_id', data.event_contact_intervention_id);
                this.container.append($clonedTemplate);

              },
              addModeratorByInfo: function(moderator) {
                return ModeratorManager.addModerator({
                  id: moderator.id,
                  event_contact_intervention_id: moderator.event_contact_intervention_id,
                  name: moderator.name,
                  intervention_status: moderator.intervention_status,
                  allow_video_distribution: moderator.allow_video_distribution,
                  moderator_type_id: moderator.moderator_type_id,
                  desired_transport_management: moderator.desired_transport_management,
                  departure_text: moderator.departure_text,
                  show_departure_text: moderator.show_departure_text,
                  return_text: moderator.return_text,
                  show_return_text: moderator.show_return_text,
                });
              },
              removeModerator: function(id) {
                this.container.find('.card[data-id="' + id + '"]').remove();
              },
            };

            moderators.forEach(function(moderator) {
              ModeratorManager.addModeratorByInfo(moderator);
            });

          });
        </script>
    @endpush


    @pushonce('js')
        <script src="{!! asset('js/dynamic_dictionnary.js') !!}"></script>
        <script src="{!! asset('js/select2AjaxWrapper.js') !!}"></script>
        <script src="{!! asset('vendor/select2/js/select2.full.min.js') !!}"></script>
        <script src="{!! asset('vendor/select2/js/i18n/fr.js') !!}"></script>
    @endpushonce

    @pushonce('css')
        <link rel="stylesheet" href="{!! asset('vendor/select2/css/select2.min.css') !!}">
    @endpushonce
</x-event-manager-layout>
