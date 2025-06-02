@props([
    'arrows' => false,
    'links' => false,
    'positions' => false,
    'event' => null, //
    'moderatorType' => 'belowSession',
    ])

@php
    use App\Accessors\Programs;
    $program = Programs::getOrganizerPrintViewCollection($event);
@endphp

@if($program)

    <style>
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            pointer-events: none;
        }

        #loadingMessage {
            color: white;
            font-size: 1.5em;
        }

    </style>
    <div id="loadingOverlay" style="display: none;">
        <button class="btn btn-blue-gray" type="button" disabled>
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            <span role="status">{{ __('front/ui.loading') }}</span>
        </button>
    </div>

    <table class="table table-bordered table-hover"
           id="program-organizer"
           data-ajax="{{route('ajax')}}">
        <thead>
        <tr>
            <th style="width:50px;">Conteneur</th>
            <th>Session Pos.</th>
            <th>Session</th>
            <th>Intervention Pos.</th>
            <th>Intervention</th>
            <th>Intervenants</th>
            <th>Début</th>
            <th>Fin</th>
        </tr>
        </thead>
        @include('events.manager.program.organizer.inc.print_table_body')
    </table>

    @php
        $isInteractive = $arrows;
    @endphp

    @if($isInteractive)
        @pushonce('js')
            <script>
                function highlight() {
                    let jTable = $('.tbody-program-container');
                    let jsonString = jTable.find('.td-person-rooms-per-day').text();
                    let personRoomsPerDay = JSON.parse(jsonString);

                    Object.keys(personRoomsPerDay).forEach(function (date) {
                        let entries = personRoomsPerDay[date];
                        for (let userId in entries) {
                            let rooms = entries[userId];
                            if (rooms.length > 1) {
                                let userCssId = date + '-' + userId;
                                let jPerson = jTable.find('.program-person[data-id="' + userCssId + '"]');
                                jPerson.removeClass('text-success text-danger');
                                jPerson.addClass('text-bg-warning');
                            }
                        }
                    });
                }

                function syncProgram(result) {
                    $('#program-organizer tbody').replaceWith(result.tbody);
                    highlight();
                }

                document.addEventListener('DOMContentLoaded', function (event) {

                    let jContainer = $('#program-organizer');

                    //----------------------------------------
                    // arrows
                    //----------------------------------------
                    jContainer.on('click', '.arrow', function () {
                        let jTd = $(this).closest('td');
                        if (jTd.hasClass('session-cell')) {
                            let sessionId = jTd.data('session-id');

                            let direction = '';
                            if ($(this).hasClass('arrow-down')) {
                                direction = 'down';
                            } else {
                                direction = 'up';
                            }

                            let formData = [
                                {name: 'action', value: 'moveProgramThing'},
                                {name: 'type', value: 'session'},
                                {name: 'direction', value: direction},
                                {name: 'sessionId', value: sessionId},
                                {name: 'eventId', value: {{$event->id}}},
                            ];
                            ajax(formData, jContainer, {
                                spinner: $('#loadingOverlay'),
                            });
                        } else if (jTd.hasClass('intervention-cell')) {
                            let interventionId = jTd.data('intervention-id');

                            let direction = '';
                            if ($(this).hasClass('arrow-down')) {
                                direction = 'down';
                            } else {
                                direction = 'up';
                            }

                            let formData = [
                                {name: 'action', value: 'moveProgramThing'},
                                {name: 'type', value: 'intervention'},
                                {name: 'direction', value: direction},
                                {name: 'interventionId', value: interventionId},
                                {name: 'eventId', value: {{$event->id}}},
                            ];

                            ajax(formData, jContainer, {
                                spinner: $('#loadingOverlay'),
                            });
                        }
                    });

                    //----------------------------------------
                    // swap positions
                    //----------------------------------------
                    jContainer.on('change', function (e) {
                        let jTarget = $(e.target);
                        if (jTarget.hasClass('select-session')) {
                            ajax(`action=moveProgramThingBySwap&event_id={{$event->id}}&type=session&id=${jTarget.data('id')}&new_position=${jTarget.val()}`, jContainer, {
                                spinner: $('#loadingOverlay'),
                            });
                            return false;
                        } else if (jTarget.hasClass('select-intervention')) {
                            ajax(`action=moveProgramThingBySwap&event_id={{$event->id}}&type=intervention&id=${jTarget.data('id')}&new_position=${jTarget.val()}`, jContainer, {
                                spinner: $('#loadingOverlay'),
                            });
                            return false;
                        }
                    });

                    highlight();

                });
            </script>
        @endpushonce

    @endif
@else
    <div class="alert alert-warning">
        {{__('programs.no_event_program_available')}}
    </div>
@endif
