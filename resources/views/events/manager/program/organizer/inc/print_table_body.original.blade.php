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
                <td class="intervention-cell" data-intervention-id="{{$intervention->id}}">
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
                </td>
                <td>{{ $intervention->room->name }}</td>
                <td>{{ $interventionStartTime->format('H:i') }}</td>
                <td>{{ $interventionEndTime->format('H:i') }}</td>
            </tr>
        @endforeach
    @endforeach
@endforeach
</tbody>