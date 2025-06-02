@php
    use App\Accessors\Dictionnaries;
    use App\Accessors\GroupAccessor;
    use App\Accessors\ProgramContainers;
@endphp
<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp


    <x-slot name="header">
        <h2 class="event-h2">
            <span>Intervention</span>
        </h2>
        <x-back.topbar.edit-combo
                :event="$event"
                :model="$data"
                :item-name="fn($m) => 'l\'intervention '. $m->name "
                :index-route="route('panel.manager.event.program.intervention.index', [
                                'event' => $event,
                                'session' => request('session') ?? null,
                            ])"
                :create-route="route('panel.manager.event.program.intervention.create', [
                                'event' => $event,
                                'session' => request('session') ?? null
                            ])"
                :delete-route="route('panel.manager.event.program.intervention.destroy', [
                                'event' => $event,
                                'intervention' => $data->id??'-1',
                            ])"
        />


    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded mb-5">

        <div class="row m-3">
            <div class="col form" id="intervention-ajax-form" data-ajax="{{ route('ajax') }}">
                <x-mfw::validation-banner/>
                <x-mfw::response-messages/>

                <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
                    @csrf
                    @if ($data->id)
                        @method('PUT')
                    @endif
                    <x-mfw::input type="hidden" name="from_session"
                                  :value="request('session') ?? null"/>

                    <div class="row pt-3">
                        <div class="col-xl-6 pe-sm-5">
                            <h4>Nouvelle contribution</h4>

                            <!-- session id -->
                            <div class="row mb-3">
                                <div class="col d-flex align-items-center ">
                                    <div class="col-6">
                                        <x-mfw::select name="intervention[main][event_program_session_id]"
                                                       label="{{ __('programs.session') }}"
                                                       :values="\App\Accessors\Programs::getSessionsSelectable($event->id)"
                                                       :affected="$error ? old('intervention.main.event_program_session_id') : $data->event_program_session_id"/>
                                    </div>
                                    <div class="col-6">

                                        <a class="btn btn-sm btn-success mx-2 mt-4"
                                           href="{{ route('panel.manager.event.program.session.create', $event) }}">
                                            <i class="fa-solid fa-circle-plus"></i>
                                            Créer une session
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="mfw-line-separator mt-4 mb-4"></div>

                            <!-- fillables -->
                            <div class="row mb-3">
                                <x-mfw::translatable-tabs datakey="intervention_texts"
                                                          :fillables="$data->fillables"
                                                          :model="$data"/>
                            </div>


                        </div>
                        <div class="col-xl-6 mb-3 position-relative">


                            <!-- sponsor -->
                            <div class="row mb-4 mfw-holder position-relative">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="w-100 me-3">
                                        <x-selectable-dictionnary key="sponsors"
                                                                  :required="false"
                                                                  name="intervention[main][sponsor_id]"
                                                                  :affected="old('intervention.main.sponsor_id', $data->sponsor_id)"/>
                                    </div>
                                    <span class="fs-4 add-dynamic dict-dynamic"
                                          data-dict="sponsors"><i
                                                class="fa-solid fa-circle-plus"></i></span>
                                </div>
                            </div>


                            <!-- divine internal comment -->
                            <div class="row mb-3">
                                <x-mfw::textarea label="{{__('programs.divine_internal_comment')}}"
                                                 height="100"
                                                 name="intervention[main][internal_comment]"
                                                 :value="$error ? old('intervention.main.internal_comment') : $data->internal_comment"/>
                            </div>


                            <!-- intervention type -->
                            <div class="row mb-3">
                                <div class="d-flex justify-content-between align-items-end mfw-holder">
                                    <div class="w-100 me-3">
                                        <x-selectable-dictionnary key="program_intervention_types"
                                                                  :required="false"
                                                                  name="intervention[main][specificity_id]"
                                                                  :affected="$error ? old('intervention.main.specificity_id') : $data->specificity_id"/>
                                    </div>
                                    <span class="fs-4 add-dynamic dict-dynamic"
                                          data-dict="program_intervention_types"><i
                                                class="fa-solid fa-circle-plus"></i></span>
                                </div>
                            </div>


                            <!-- duration -->
                            <div class="row mb-3">
                                <x-mfw::input type="number"
                                              name="intervention[main][duration]"
                                              :value="$error ? old('intervention.main.duration') : $data->duration"
                                              label="Durée en minutes *"/>
                            </div>

                            <!-- intervention_timing_details -->
                            <div class="row mb-3">
                                <x-mfw::textarea label="{{__('programs.intervention_timing_details')}}"
                                                 height="100"
                                                 name="intervention[main][intervention_timing_details]"
                                                 :value="$error ? old('intervention.main.intervention_timing_details') : $data->intervention_timing_details"/>
                            </div>


                            @if(false)
                                <!-- preferred_start_time -->
                                <div class="row mb-3">
                                    @php
                                        $defaultDate = '';
                                        if($error){
                                            $defaultDate = ',defaultDate='.old('intervention.main.preferred_start_time');
                                        }
                                        elseif($data->id && $data->preferred_start_time){
                                            $defaultDate = ',defaultDate='.\App\Helpers\DateHelper::timeToHoursMinutesTime($data->preferred_start_time);
                                        }
                                    @endphp
                                    <x-mfw::datepicker name="intervention[main][preferred_start_time]"
                                                       label="{{__('programs.preferred_start_time')}}"
                                                       config="enableTime=true,allowInput=true,noCalendar=true,minuteIncrement=01,defaultHour=09,dateFormat=H:i{{$defaultDate}}"/>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="row mb-3 p-0">
                        <label for="intervention_orators_id"
                               class="form-label d-flex gap-2 align-items-center">
                            <span>Intervenants</span>
                            @if($data->id)
                                <a href="{{route('panel.accounts.create', [
                                'intervention_id' => $data->id,
                            ])}}"
                                   class="text-success fs-5"><i class="bi bi-plus-circle-fill"></i></a>
                            @endif
                        </label>
                        <select name="intervention[orators][]"
                                id="intervention_orators_id"
                                multiple="multiple">
                        </select>

                    </div>
                    <div class="mb-3 d-flex gap-3 flex-wrap justify-content-between"
                         id="orator-container">
                    </div>


                </form>
            </div>

        </div>
    </div>


    <template id="tpl-orator-transport-info">
        <div x-data x-ref="container" class="card" style="width:48%;">
            <input type="hidden" name="intervention[orators_info][id][]" value="">

            <div class="card-header">
                <a href="#" class="orator-full-name">full name</a>
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
                                <select name="intervention[orators_info][status][]"
                                        class="intervention-statuses">
                                    @foreach(\App\Enum\EventProgramParticipantStatus::translations() as $value => $label)
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
                                <select
                                        name="intervention[orators_info][allow_video_distribution][]"
                                        class="select-allow-video-distribution"
                                >
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Autorisation de distribution de pdf</td>
                        <td class="allow-pdf-distribution">
                            <div class="d-flex align-items-center">
                                <select class="select-allow-pdf-distribution"
                                        name="intervention[orators_info][allow_pdf_distribution][]">
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

            $(document).ready(function () {

                activateEventManagerLeftMenuItem('interventions');

                const jAjaxSelector = $('#intervention-ajax-form');
                $('#intervention_main__event_program_session_id').on('change', function () {
                    ajax('action=getSessionInfo&session_id=' + $(this).val(), jAjaxSelector, {
                        successHandler: function (response) {
                            if (response.place_room_id) {
                                let place_id = response.place_room.place_id;
                                $('#intervention_main__place_room_id').html(
                                    `<option value="${response.place_room_id}">${response.place_room.name.fr}</option>`,
                                );
                                $('#intervention_main__place_id').val(place_id);
                            } else {
                                $('#intervention_main__place_room_id').html('<option value="">Veuillez sélectionner un lieu d\'abord</option>');
                                $('#intervention_main__place_id').val('');
                            }
                            $('#session_main__group_id').val(response.group_id);
                        },
                    });
                });

                //----------------------------------------
                // orators
                //----------------------------------------
                const orators = {!! \Illuminate\Support\Js::from($orators) !!};
                let alreadySelectedOratorIds = [];
                orators.forEach(function (orator) {
                    alreadySelectedOratorIds.push(orator.id);
                });

                let jOratorContainer = $('#orator-container');

                $('#intervention_orators_id').select2AjaxWrapper({
                    placeholder: 'Veuillez sélectionner les intervenants',
                    defaultValues: {!! \Illuminate\Support\Js::from(array_column($orators, 'name', 'id')) !!},
                    multiple: true,
                    language: 'fr',
                    ajax: {
                        url: "{{ route('ajax') }}",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term,
                                event_id: {{$event->id}},
                                intervention_id: {{$data?->id ?? "null"}},
                                alreadySelectedOratorIds: alreadySelectedOratorIds,
                                action: 'select2InterventionParticipantsInfo',
                            };
                        },
                    },
                });

                $('#intervention_orators_id').on('select2:select', function (e) {
                    OratorManager.addOratorByInfo(e.params.data.info);
                    alreadySelectedOratorIds.push(e.params.data.id);
                });

                $('#intervention_orators_id').on('select2:unselect', function (e) {
                    const deselectedId = e.params.data.id;
                    alreadySelectedOratorIds = alreadySelectedOratorIds.filter(id => id != deselectedId);
                    OratorManager.removeOrator(deselectedId);
                });

                const OratorManager = {
                    template: $('#tpl-orator-transport-info').html(),
                    container: jOratorContainer,

                    addOrator: function (data) {
                        let $clonedTemplate = $(this.template);

                        let jFullName = $clonedTemplate.find('.orator-full-name');

                        jFullName.text(data.name);
                        jFullName.attr('href', "{{route('panel.manager.event.event_contact.edit', [
                    'event' => $event,
                    'event_contact' => "xxx",
                ])}}".replace('xxx', data.id));
                        $clonedTemplate.find('.desired-transport-management').text(data.desired_transport_management);

                        $clonedTemplate.find('.allow-video-distribution select').val(data.allow_video_distribution);
                        $clonedTemplate.find('.allow-pdf-distribution select').val(data.allow_pdf_distribution);
                        $clonedTemplate.find('.intervention-statuses').val(data.intervention_status);

                        $clonedTemplate.find('.departure-text').text(data.departure_text);
                        $clonedTemplate.find('.return-text').text(data.return_text);
                        $clonedTemplate.find('.departure-info').toggle(data.show_departure_text);
                        $clonedTemplate.find('.return-info').toggle(data.show_return_text);

                        $clonedTemplate.attr('data-id', data.id);
                        $clonedTemplate.attr('data-event_contact_intervention_id', data.event_contact_intervention_id);
                        this.container.append($clonedTemplate);

                    },
                    addOratorByInfo: function (orator) {
                        return OratorManager.addOrator({
                            id: orator.id,
                            event_contact_intervention_id: orator.event_contact_intervention_id,
                            name: orator.name,
                            intervention_status: orator.intervention_status,
                            allow_pdf_distribution: orator.allow_pdf_distribution,
                            allow_video_distribution: orator.allow_video_distribution,
                            desired_transport_management: orator.desired_transport_management,
                            departure_text: orator.departure_text,
                            show_departure_text: orator.show_departure_text,
                            return_text: orator.return_text,
                            show_return_text: orator.show_return_text,
                        });
                    },
                    removeOrator: function (id) {
                        this.container.find('.card[data-id="' + id + '"]').remove();
                    },
                };

                orators.forEach(function (orator) {
                    OratorManager.addOratorByInfo(orator);
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
