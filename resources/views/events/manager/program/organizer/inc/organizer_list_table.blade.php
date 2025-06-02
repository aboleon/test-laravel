<div class="container intervention-container mt-4">
    <div class="mb-3">
        <label>
            <input type="checkbox" id="toggleFixedInterventions">
            {{__('programs.hide_custom_interventions')}}
        </label>
    </div>

    <ul id="sortable">
        @foreach($program as $day)
            <li class="day">
                <div>{{ $day->datetime_start->format('Y-m-d') }}</div>
                <ul>
                    @php
                        $currentStartTime = clone $day->datetime_start;
                    @endphp
                    @foreach($day->sessions as $session)
                        <li class="session">
                            <div class="session-header session-handle">
                                ☰ {{ $session->name }}
                            </div>
                            <table class="intervention-table">
                                <tbody>
                                @foreach($session->interventions as $intervention)
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
                                        <td class="intervention-handle">☰ {{ $intervention->name }}</td>
                                        <td>{{ $intervention->room->name }}</td>
                                        <td>{{ $session->position }}</td>
                                        <td>{{ $intervention->position }}</td>
                                        <td>{{ $interventionStartTime->format('H:i') }}</td>
                                        <td>{{ $interventionEndTime->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>







@pushonce('js')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>

@endpushonce


@push('js')
<script>

  //----------------------------------------
  // hide/show custom interventions
  //----------------------------------------
  $('#toggleFixedInterventions').on('change', function() {
    if ($(this).is(':checked')) {
      $('.custom-intervention-row').addClass('d-none');
    } else {
      $('.custom-intervention-row').removeClass('d-none');
    }
  });


  //----------------------------------------
  // sortable
  //----------------------------------------
  $('#sortable').nestedSortable({
    handle: '.session-handle',
    items: 'li',
    toleranceElement: '> div',
    listType: 'ul',
    maxLevels: 3,
    protectRoot: false,
    rootID: 'sortable'
  });




</script>
@endpush

