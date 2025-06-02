<tbody>
@foreach($program as $day)
    @php
        $currentStartTime = clone $day->datetime_start;
        $daySpan = 0;
    @endphp
    @foreach($day->sessions as $session)
        @php $daySpan += count($session->interventions); @endphp
    @endforeach

    @foreach($day->sessions as $session)
        @foreach($session->interventions as $index => $intervention)
            @php
                $drawEmptyCell = is_array($allowedInterventions) && !in_array($intervention->id, $allowedInterventions);
                $interventionStartTime = $currentStartTime->copy();
                if ($intervention->preferred_start_time) {
                    $interventionStartTime = $intervention->preferred_start_time;
                } else {
                    $currentStartTime->addMinutes($intervention->duration);
                }
                $interventionEndTime = $interventionStartTime->copy()->addMinutes($intervention->duration);
            @endphp

            <tr class="intervention @if($intervention->preferred_start_time) custom-intervention-row @endif">
                @if($index === 0 && $session === $day->sessions->first())
                    <td rowspan="{{ $daySpan }}"
                        class="date-cell">{{ $day->datetime_start->format('Y-m-d') }}</td>
                @endif

                @if($index === 0)
                    <td class="session-cell"
                        rowspan="{{ count($session->interventions) }}"
                        data-session-id="{{$session->id}}">
                        {{ $session->name }}
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

                <td class="intervention-cell {{$drawEmptyCell?'empty-cell':''}}"
                    data-intervention-id="{{$intervention->id}}">
                    @if($drawEmptyCell)
                        &nbsp; <!-- empty cell -->
                    @else
                        @if($links)
                            <a href="{{ route('panel.manager.event.program.intervention.edit', [
        'event'=> $day->event_id,
        'intervention' => $intervention->id,
    ])  }}">
                                {{ $intervention->name }}
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
                    @endif
                </td>

                <td>
                    @if(!$drawEmptyCell)
                        {{ $intervention->room->name }}
                    @endif
                </td>

                <td>
                    @if(!$drawEmptyCell)
                        {{ $interventionStartTime->format('H:i') }}
                    @endif
                </td>

                <td>
                    @if(!$drawEmptyCell)
                        {{ $interventionEndTime->format('H:i') }}
                    @endif
                </td>
            </tr>
        @endforeach
    @endforeach
@endforeach
</tbody>
