@php
    use App\Accessors\GroupAccessor;
    $data = $event;
@endphp
<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">
            <span>Programme</span> &raquo;
            <span>Conteneurs</span>
        </h2>
        <x-back.topbar.edit-combo
                :event="$event"
                :show-index-route="false"
                :use-create-route="false"
                :show-delete-btn="false"
        />
    </x-slot>

    <x-mfw::validation-errors/>
    @if($event->has_program)

        <div class="shadow p-4 bg-body-tertiary rounded">

            <div class="container mt-4">
                <form method="post" action="{{ $route }}" id="wagaia-form" novalidate>
                    @csrf
                    @method('PUT')

                    @php
                        $days = (new \App\Helpers\DateHelper())->listDaysBetweenDates($data->starts, $data->ends, config('app.date_display_format'));
                        $rooms = $data->rooms->pluck('name', 'id')->toArray();
                        $oldData = old("event.program_day_rooms");
                    @endphp
                    <div id="program_days_list" data-ajax="{{route("ajax")}}" class="d-block">
                        <div class="mfw-line-separator mt-4 mb-4 messages"></div>
                        <div class="d-flex gap-2 mb-4">

                            <button class="btn btn-sm btn-blue-gray btn-refresh-days">
                                <i class="fa-solid fa-sync btn-refresh-days"></i>
                                Rafraîchir
                            </button>
                            <button class="btn btn-sm btn-green btn-add-item">
                                <i class="fa-solid fa-plus btn-add-item"></i>
                                Ajouter un conteneur
                            </button>
                            <x-spinner id="event-program-spinner"/>
                        </div>
                        <div class="row" id="program_days_list-items"></div>
                    </div>


                    <template id="program_days_list-template">
                        <div class="col-12 mb-3 d-flex gap-1 align-items-center program-item">


                            <x-mfw::select name="event[program_day_rooms][day][]"
                                           :values="[]"
                                           class="select-day"
                                           defaultselecttext="--- Sélectionner un jour ---"
                                           affected="0"/>

                            <x-mfw::datepicker
                                    name="event[program_day_rooms][hour][]"
                                    class="input-hour"
                                    value="50"
                                    config="enableTime=true,noCalendar=true,minuteIncrement=01,defaultHour=00,dateFormat=H:i,defaultDate=09:00"/>


                            <x-mfw::select name="event[program_day_rooms][place_id][]"
                                           class="select-place"
                                           :values="$places"
                                           defaultselecttext="--- Sélectionner un lieu ---"
                                           :affected="$data->place_id"/>

                            <x-mfw::select name="event[program_day_rooms][room_id][]"
                                           class="select-room"
                                           :values="$rooms"
                                           defaultselecttext="--- Sélectionner une salle ---"
                            />

                            <input type="hidden"
                                   class="input-rooms"
                                   name="event[program_day_rooms][rooms][]"
                                   value="{{ json_encode($rooms) }}"/>


                            <button class="btn btn-sm btn-red btn-remove-item">
                                <i class="fas fa-remove btn-remove-item"></i></button>
                            <a class="btn btn-sm btn-green btn-add-dynamic"
                               title="Ajouter une salle"
                               data-placement="top"
                               data-toggle="tooltip"
                               data-bs-toggle="modal"
                               data-bs-target="#mfwDynamicModal"
                               data-modal-on-success="appendRoom"
                               data-modal-content-url="{{ route('panel.modal', ['requested' => 'createPlaceRoom', 'selectable' => 'session_main__place_room_id', 'place_id' => $data->place_id]) }}"
                            >
                                <i class="fa-solid fa-circle-plus"></i></a>


                        </div>
                    </template>

                </form>
            </div>


        </div>


        @if($data->id)
            @push('js')
                <script>
                    $(document).ready(function () {

                        //----------------------------------------
                        // functions
                        //----------------------------------------
                        function addLine(data) {
                            const clone = document.importNode(templateContent, true);

                            const jDay = $(clone).find('.select-day');
                            const jHour = $(clone).find('.input-hour');
                            const jPlace = $(clone).find('.select-place');
                            const jRoom = $(clone).find('.select-room');
                            const jRooms = $(clone).find('.input-rooms');
                            const JAddRoomBtn = $(clone).find('.btn-add-dynamic');

                            jDay.empty();
                            days.forEach(function (day) {
                                let sSelected = day === data.day ? ' selected' : '';
                                jDay.append('<option value="' + day + '" ' + sSelected + '>' + day + '</option>');
                            });

                            jHour.attr('data-config', 'enableTime=true,noCalendar=true,minuteIncrement=01,defaultHour=00,dateFormat=H:i,defaultDate=' + data.hour);

                            jPlace.val(data.place_id);

                            if (data.room_id) {
                                jRoom.find('option:not(:first)').remove();
                                let rooms = {};
                                Object.entries(data.rooms).forEach(function ([id, room]) {
                                    let sSelected = id == data.room_id ? ' selected' : '';
                                    jRoom.append('<option value="' + id + '" ' + sSelected + '>' + room + '</option>');
                                    rooms[id] = room;
                                });
                                jRooms.val(JSON.stringify(rooms));
                            }

                            PlaceRoom.bindTooltips(JAddRoomBtn);
                            PlaceRoom.bindModalUrl(JAddRoomBtn, data.place_id);

                            console.log('add', clone, jItemsContainer);
                            jItemsContainer.append(clone);
                        }

                        function refresh() {
                            jItemsContainer.empty();
                            ajax('action=getProgramDayRooms&event_id={{ $data->id }}', jContainer, {
                                spinner: jSpinner,
                                successHandler: function (r) {
                                    const daysInfo = r.days;
                                    daysInfo.forEach(function (day) {
                                        addLine(day);
                                    });
                                    setDatepicker();
                                    return true;
                                },
                            });
                        }

                        function refreshFromOldData(d) {
                            let formattedData = [];

                            for (let i = 0; i < d.day.length; i++) {
                                formattedData.push({
                                    day: d.day[i],
                                    hour: d.hour[i],
                                    room_id: d.room_id[i],
                                    place_id: d.place_id[i],
                                    rooms: JSON.parse(d.rooms[i]),
                                });
                            }

                            formattedData.forEach(function (day) {
                                addLine(day);
                            });
                            setDatepicker();
                        }

                        //----------------------------------------
                        // main
                        //----------------------------------------
                        const days = {!! json_encode($days) !!};
                        const oldData = {!! json_encode($oldData) !!};
                        const jContainer = $('#program_days_list');
                        const jSpinner = $('#event-program-spinner');
                        const defaultDay = "{{ $data->starts  }}";
                        interact.checkboxTogglesTarget('#event_config__has_program :checkbox', '#program_days_list');

                        const templateContent = document.querySelector('#program_days_list-template').content;
                        const jItemsContainer = $('#program_days_list-items');

                        jContainer.on('click', function (e) {
                            const jTarget = $(e.target);
                            if (jTarget.hasClass('btn-refresh-days')) {
                                refresh();
                            } else if (jTarget.hasClass('btn-remove-item')) {
                                jTarget.closest('.program-item').remove();
                            } else if (jTarget.hasClass('btn-add-item')) {
                                addLine({
                                    day: defaultDay,
                                    hour: '09:00',
                                    place_id: {{ $data->place_id }},
                                    room_id: 0,
                                });
                                setDatepicker();
                            }
                            return false;
                        });

                        jContainer.on('change', function (e) {
                            const jTarget = $(e.target);
                            if (jTarget.hasClass('select-place')) {
                                let jItem = jTarget.closest('.program-item');
                                let jPlace = jItem.find('.select-place');
                                let placeId = jPlace.val();

                                ajax('action=getRoomsByPlaceId&id=' + placeId, jContainer, {
                                    spinner: jSpinner,
                                    successHandler: function (r) {
                                        let jRoom = jItem.find('.select-room');
                                        jRoom.find('option:not(:first)').remove();
                                        Object.entries(r.items).forEach(function ([id, room]) {
                                            jRoom.append('<option value="' + id + '">' + room + '</option>');
                                        });
                                        jRoom.find('option:first').prop('selected', true);
                                        PlaceRoom.bindModalUrl(jItem.find('.btn-add-dynamic'), placeId);
                                        return true;
                                    },
                                });
                            }
                            return false;
                        });

                        if (oldData) {
                            refreshFromOldData(oldData);
                        } else {
                            refresh();
                        }
                    });

                    const PlaceRoom = {
                        bindTooltips:function(el) {
                            el.tooltip('dispose');
                            el.tooltip();
                        },
                        bindModalUrl: function(el, placeId){
                            console.log(el.attr('data-modal-content-url'));
                            let url = new URL(el.data('modal-content-url'));
                            let params = url.searchParams;
                            params.set('place_id', placeId);
                            url.search = params.toString();
                            el.attr('data-modal-content-url', url.toString()).change();
                        }
                    };

                    function appendRoom(r){
                        const placeId = r.input.place_id;
                        const roomOption = '<option value="' + r.room_id + '">' + r.room_name + '</option>';

                        $('.select-place').filter(function() {
                            return $(this).val() === placeId;
                        }).each(function() {
                            $(this)
                                .closest('.program-item')
                                .find('.select-room')
                                .append(roomOption);
                        });

                        $('#mfwDynamicModal').modal('hide');
                    }
                </script>
            @endpush
        @endif

    @else
        <div class="alert alert-danger">
            <p>
                Les programmes ne sont pas activés pour cet événement.
            </p>
        </div>
    @endif


    @push('css')
        <style>
            #program_days_list-items input {
                width: auto;
            }
        </style>
    @endpush

    @push('modals')
        @include('mfw-modals.launcher')
    @endpush

</x-event-manager-layout>
