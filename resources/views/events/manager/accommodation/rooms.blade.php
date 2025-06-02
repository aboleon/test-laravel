<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2 fs-4">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Chambres</span>
        </h2>
        <x-back.topbar.edit-combo
                :event="$event"
                :index-route="route('panel.manager.event.accommodation.index', $event)"
                :use-create-route="false"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        @include('events.manager.accommodation.tabs')
        <form method="post" action="{{ route('panel.manager.event.accommodation.rooms.update', [$event, $accommodation]) }}" id="wagaia-form">
            @csrf
            @method('PUT')
            @if ($accommodation->roomGroups->isNotEmpty())
                @foreach($accommodation->roomGroups as $group)
                    <x-accommodation-room-group :model="$group"/>
                @endforeach
            @endif
            <x-mfw::alert message="Aucune chambre n'est configurée." type="danger rooms-notice {{  $accommodation->roomGroups->isEmpty() ? '' : 'd-none' }}"/>


        </form>
        <button class="btn btn-sm btn-success mt-3" id="add-room-group" type="button">
            <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter une catégorie
        </button>

        <template id="room-group-template">
            <x-accommodation-room-group :model="new \App\Models\EventManager\Accommodation\RoomGroup()"/>
        </template>
        <template id="room-template">
            <x-accommodation-room :room="new \App\Models\EventManager\Accommodation\Room()"/>
        </template>
    </div>

    <div class="modal fade" id="rooms_errors_modal" tabindex="-1" aria-labelledby="rooms_errors_modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="rooms_errors_modalLabel">Erreur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body" id="rooms_errors_modalBody">
                    <div class="rooms-notice notices d-none">
                        <x-mfw::alert message="Les combinaisons chambre / capacité doivent être uniques."/>
                    </div>
                    <div class="room-groups-notice notices d-none">
                        <x-mfw::alert message="Les intitulés des catégories (groupes) de chambres doivent être saisies"/>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center" id="rooms_errors_modalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Compris</button>
                </div>
            </div>
        </div>
    </div>


    @push('callbacks')
        <script>

            function ajaxPostDeleteGroup(result) {
                if (!result.hasOwnProperty('error')) {
                    setTimeout(function () {
                        $(result.input.identifier).remove();
                        roomNotice();
                    }, 1000);
                }
            }

            function ajaxDeleteRoom() {
                $('.delete_room').off().on('click', function () {
                    $('.messages').html('');
                    let id = $(this).attr('data-model-id'),
                        identifier = $(this).attr('data-identifier');
                    $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                    if (id.length < 1 || isNaN(id)) {
                        $(identifier).remove();
                    } else {
                        ajax('action=removeAccommodationRoom&id=' + Number(id) + '&identifier=' + identifier, $(identifier).closest('.room-group'));
                    }
                });
            }

            function roomNotice() {
                if ($('.room-group').length < 1) {
                    $('.rooms-notice').removeClass('d-none');
                }
            }

            function ajaxDeleteRoomGroup() {
                $('.delete_room_group').off().on('click', function () {
                    $('.messages').html('');
                    let id = $(this).attr('data-model-id'),
                        identifier = $(this).attr('data-identifier');
                    $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                    if (id.length < 1 || isNaN(id)) {
                        $(identifier).remove();
                        roomNotice();
                    } else {
                        ajax('action=removeRoomGroup&id=' + Number(id) + '&identifier=' + identifier, $(identifier));
                    }
                });
            }
        </script>
    @endpush

    @push('js')
        <script>
            $(function () {
                const rm = {
                    form: function () {
                        return $('#wagaia-form');
                    },
                    add: function () {
                        $('#add-room-group').click(function () {
                            $('.rooms-notice').addClass('d-none');
                            rm.form().append($('#room-group-template').html());
                            rm.setGroupId();
                            rm.addRoom();
                        });
                    },
                    setGroupId: function () {
                        this.attributeUpdater(rm.form().find('.room-group').last(), 'new-room-group', guid());
                    },
                    attributeUpdater: function (target, old_id, new_id) {

                        let ul = target.find('#new-room-group_tabs');
                        console.log(ul);
                        ul.attr('id', ul.attr('id').replace(old_id, new_id));

                        target.find('button.nav-link').each(function () {
                            console.log($(this).attr('data-bs-target'));
                            $(this).attr('id', $(this).attr('id').replace(old_id, new_id));
                            $(this).attr('data-bs-target', $(this).attr('data-bs-target').replace(old_id, new_id));
                            $(this).attr('aria-controls', $(this).attr('aria-controls').replace(old_id, new_id));
                        });

                        target.find('.tab-pane').each(function () {
                            $(this).attr('id', $(this).attr('id').replace(old_id, new_id));
                            $(this).attr('aria-labelledby', $(this).attr('aria-labelledby').replace(old_id, new_id));
                            rm.updateFormTags($(this), new_id);
                        });

                        target.addClass('room-group-' + new_id).find('.delete-room-group').attr('data-identifier', '.room-group-' + new_id);
                        target.find('.key').val(new_id);
                    },
                    attributeRoomUpdater: function (target) {

                        let new_id = target.parents('.room-group').find('.key').first().val(),
                            sub_id = guid();

                        if (!isNaN(new_id)) {
                            new_id = 'room_group_' + new_id;
                        }

                        target.find('textarea, input, select').each(function () {
                            $(this).attr('name', new_id + '[' + $(this).attr('name') + '][]');
                            if ($(this).attr('id') !== undefined) {
                                $(this).attr('id', $(this).attr('id') + '_' + new_id + '_' + sub_id);
                            }
                        });

                        target.find('label').each(function () {
                            $(this).attr('for', $(this).attr('for') + '_' + new_id + '_' + sub_id);
                        });

                        target.addClass('room-' + sub_id).find('a').attr('data-identifier', '.room-' + sub_id);
                    },
                    addRoom: function () {
                        rm.selectRoom();
                        $('.room-group .add-room').off().on('click', function () {
                            let c = $(this).prev('.rows');
                            c.append($('#room-template').html());
                            rm.attributeRoomUpdater(c.find('.room').last());
                            rm.selectRoom();

                        });
                    },
                    updateFormTags: function (target, new_id) {
                        target.find('textarea, input, select').each(function () {
                            let name = $(this).attr('name').split('[');
                            $(this).attr('name', new_id + '[' + name[0] + '][' + name[1]);
                            $(this).attr('id', $(this).attr('id') + '_' + new_id);
                        });

                        target.find('label').each(function () {
                            $(this).attr('for', $(this).attr('for') + '_' + new_id);
                        });
                    },
                    setIds: function () {
                        let groups = $('.room-group');

                        if (!groups.length) {
                            return false;
                        }

                        groups.each(function () {
                            let random_id = guid();
                            $(this).addClass('room-group-' + random_id).find('a.delete-room-group').attr('data-identifier', '.room-group-' + random_id);

                            let target = $(this).find('.tab-pane');
                            rm.updateFormTags(target, 'room_group_' + $(this).find('.key').first().val());

                            let rooms = $(this).find('.rooms > .rows > .room');
                            if (rooms.length) {
                                rooms.each(function () {
                                    rm.attributeRoomUpdater($(this));
                                });
                            }

                        });
                    },
                    selectRoom: function () {
                        $('div.room select').off().change(function () {
                            let selects = $(this).closest('.rows').find('select').not(this),
                                selected = $(this);
                            selects.each(function () {
                                let val = $(this).val();
                                if (val !== '' && val === selected.val()) {
                                    selected.addClass('is-invalid');
                                } else {
                                    selected.removeClass('is-invalid');
                                }
                            });

                        });
                    },
                    preventInvalidSubmit: function () {

                        let modal = new bootstrap.Modal(document.getElementById('rooms_errors_modal'));

                        $('button[form=wagaia-form]').click(function (e) {

                                $('#rooms_errors_modal .notices').addClass('d-none');

                                let tab = $('.tab-pane').first(),
                                    input = tab.find(':text').first(),
                                    val = input.val();
                                if ($.trim(val) === '') {
                                    input.addClass('is-invalid');
                                } else {
                                    input.removeClass('is-invalid');
                                }

                                if ($('.tab-pane').find('.is-invalid').length) {
                                    e.preventDefault();
                                    $('#rooms_errors_modal .room-groups-notice').removeClass('d-none');
                                    modal.show();
                                }

                                if ($('select.is-invalid').length) {
                                    e.preventDefault();
                                    $('#rooms_errors_modal .rooms-notice').removeClass('d-none');
                                    modal.show();
                                }
                            },
                        );
                    },
                    init: function () {
                        this.preventInvalidSubmit();
                        this.add();
                        this.addRoom();
                        this.setIds();
                    },
                };
                rm.init();
            });
        </script>
    @endpush
    <x-mfw::save-alert />
</x-event-manager-layout>
