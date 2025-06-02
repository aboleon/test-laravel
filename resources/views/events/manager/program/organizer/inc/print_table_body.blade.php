@php
    use App\Enum\EventProgramParticipantStatus;
    $personRoomsPerDay = [];
    $orderCancellationPill = view('components.back.order-cancellation-pill', [
      'style' => 'program',
      'width' => 20,
      'height' => 20
    ])->render();
@endphp
<tbody class="tbody-program-container">
@foreach($program as $day)
    @php
        $currentStartTime = clone $day->datetime_start;
        $daySpan = 0;
        $printedDate = false;
    @endphp
    @foreach($day->sessions as $session)
        @php $daySpan += count($session->interventions); @endphp
    @endforeach

    @foreach($day->sessions as $session)
        @php
            $dayDisplay = $day->datetime_start->format(config("app.date_display_format"));
            if(false === array_key_exists($dayDisplay, $personRoomsPerDay)){
                $personRoomsPerDay[$dayDisplay] = [];
            }

            $roomFullName = $day->room->place->name . ' - ' . $day->room->name;
            $sessionStartTime = $session->interventions->first()?->start?->format('H:i');
            $sessionEndTime = $session->interventions->last()?->end?->format('H:i');
            $sessionTime = $sessionStartTime . ' - ' . $sessionEndTime;
        @endphp
        @foreach($session->interventions as $index => $intervention)
            <tr class="intervention @if($intervention->preferred_start_time) custom-intervention-row @endif">
                @if(!$printedDate)
                    <td rowspan="{{ $daySpan }}"
                        class="date-cell fs-6">
                        <b>{{ $dayDisplay }}</b>
                        <br>
                        <span class="small">{{ $day->datetime_start->format("H:i") }}</span>
                        <br>
                        <span class="text-danger">{{ $day->room->place->name }}</span>
                        <br>
                        <span>{{ $day->room->name }}</span>
                        <x-front.debugmark :title="$day->id"/>
                    </td>
                    @php
                        $printedDate = true;
                    @endphp
                @endif
                @if($index === 0)
                    @if($positions)
                        <td rowspan="{{ count($session->interventions) }}">
                            @php
                                $nbSessions = count($day->sessions);
                            @endphp
                            <select class="select-session" data-id="{{$session->id}}">
                                @for($i = 1; $i <= $nbSessions; $i++)
                                    <option value="{{$i}}"
                                            @if($session->position == $i) selected @endif>{{$i}}</option>
                                @endfor
                            </select>
                        </td>
                    @endif


                    <td
                            @class([
                              "session-cell" => true,
                              "bg-danger" => (bool)$session->is_catering,
                              "bg-success" => (bool)$session->is_placeholder,
                            ])
                            rowspan="{{ count($session->interventions) }}"
                            data-session-id="{{$session->id}}">
                        @if($links)
                            <a href="{{ route('panel.manager.event.program.session.edit', [
        'event'=> $day->event_id,
        'session' => $session->id,
    ])  }}">
                                @endif

                                {{ $session->name }}
                                @if($links)
                            </a>
                        @endif
                        <br>
                        <span class="small">{{ $sessionTime }}</span>

                        @php
                            $moderatorNames = $session->moderators->map(function ($contact) use (&$personRoomsPerDay, $dayDisplay, $roomFullName, $orderCancellationPill) {
                                $status = $contact->pivot->status;
                                $userId = $contact->user->id;
                                $sClass = match($status){
                                    EventProgramParticipantStatus::VALIDATED->value => 'text-success',
                                    EventProgramParticipantStatus::DENIED->value => 'text-danger',
                                    default => '',
                                };

                                if(false === array_key_exists($userId, $personRoomsPerDay[$dayDisplay])){
                                    $personRoomsPerDay[$dayDisplay][$userId] = [];
                                }
                                if(false === in_array($roomFullName, $personRoomsPerDay[$dayDisplay][$userId])){
                                    $personRoomsPerDay[$dayDisplay][$userId][] = $roomFullName;
                                }

                                $s = '<span data-id="'. $dayDisplay . "-" . $userId .'" class="program-person d-inline-flex '. $sClass .'">' .
                                $contact->user->last_name . ' ' . $contact->user->first_name;
                                if($contact->order_cancellation){
                                    $s .= $orderCancellationPill;
                                }
                                $s .= '</span>';
                                return $s;
                            });


                            $moderatorNames = $moderatorNames->sort();
                        @endphp
                        @if($moderatorNames->isNotEmpty())
                            <br>
                            Modérateur(s):
                            {!! $moderatorNames->implode(', ') !!}
                        @endif

                        @if($session->sponsor)
                            <br>Sponsor: {{ $session->sponsor->name }}
                        @endif

                        @if($arrows)
                            <div class="arrow arrow-up">
                                <i class="fa-solid fa-arrow-up"></i>
                            </div>
                            <div class="arrow arrow-down">
                                <i class="fa-solid fa-arrow-down"></i>
                            </div>
                        @endif
                    </td>

                @endif

                @if($positions)
                    <td>
                        @php
                            $nbInterventions = count($session->interventions);
                        @endphp
                        <select class="select-intervention" data-id="{{$intervention->id}}">
                            @for($i = 1; $i <= $nbInterventions; $i++)
                                <option value="{{$i}}"
                                        @if($intervention->position == $i) selected @endif>{{$i}}</option>
                            @endfor
                        </select>
                    </td>
                @endif
                <td
                        @class([
                          "intervention-cell" => true,
                          "bg-danger" => (bool)$intervention->is_catering,
                          "bg-success" => (bool)$intervention->is_placeholder,
                        ])
                        data-intervention-id="{{$intervention->id}}">
                    @if($links)
                        <a href="{{ route('panel.manager.event.program.intervention.edit', [
        'event'=> $day->event_id,
        'intervention' => $intervention->id,
    ])  }}">
                            {{ $intervention->name }} ({{ $intervention->session->programDay->room->name }})
                        </a>
                    @else
                        {{ $intervention->name }}
                    @endif
                    @if($arrows)
                        <div class="arrow arrow-up">
                            <i class="fa-solid fa-arrow-up"></i>
                        </div>
                        <div class="arrow arrow-down">
                            <i class="fa-solid fa-arrow-down"></i>
                        </div>
                    @endif
                </td>

                <td>
                    @php
                        $oratorNames = $intervention->orators->map(function ($contact) use(&$personRoomsPerDay, $dayDisplay, $roomFullName, $orderCancellationPill) {
                            $status = $contact->pivot->status;
                            $userId = $contact->user->id;
                            $sClass = match($status){
                                EventProgramParticipantStatus::VALIDATED->value => 'text-success',
                                EventProgramParticipantStatus::DENIED->value => 'text-danger',
                                default => '',
                            };


                            if(false === array_key_exists($userId, $personRoomsPerDay[$dayDisplay])){
                                $personRoomsPerDay[$dayDisplay][$userId] = [];
                            }
                            if(false === in_array($roomFullName, $personRoomsPerDay[$dayDisplay][$userId])){
                                $personRoomsPerDay[$dayDisplay][$userId][] = $roomFullName;
                            }

                            $s = '<span data-id="'. $dayDisplay . "-" . $userId .'" class="program-person d-inline-flex '. $sClass .'">' . $contact->user->last_name . ' ' . $contact->user->first_name;
                            if($contact->order_cancellation){
                                $s .= $orderCancellationPill;
                            }
                            $s .= '</span>';
                            return $s;
                        });
                        $oratorNames = $oratorNames->sort();
                    @endphp
                    @if($oratorNames->isNotEmpty())
                        {!! $oratorNames->implode(', ') !!}
                    @endif
                </td>
                <td>{{ $intervention->start->format('H:i') }}</td>
                <td>{{ $intervention->end->format('H:i') }}</td>
            </tr>
        @endforeach
    @endforeach
@endforeach
<tr class="d-none">
    <td class="td-person-rooms-per-day"><?php echo json_encode($personRoomsPerDay); ?></td>
</tr>
</tbody>
