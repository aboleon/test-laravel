<div class="container mt-4">
    <div class="mb-3">
        <label>
            <input type="checkbox" id="toggleFixedInterventions">

            {{__('programs.hide_custom_interventions')}}
        </label>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>{{__('programs.date')}}</th>
            <th>{{__('programs.session')}}</th>
            <th>{{__('programs.intervention')}}</th>
            <th>{{__('programs.location')}}</th>
            <th>{{__('programs.session_position')}}</th>
            <th>{{__('programs.intervention_position')}}</th>
            <th>{{__('programs.preferred_start_time')}}</th>
            <th>{{__('ui.start')}}</th>
            <th>{{__('ui.end')}}</th>
        </tr>
        </thead>
        <tbody id="sortable">
        @foreach($program as $day)
            @php
                $currentStartTime = clone $day->datetime_start;
            @endphp

            @foreach($day->sessions as $session)
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
                        <td>{{ $day->datetime_start->format('Y-m-d') }}</td>
                        <td class="session-handle">☰ {{ $session->name }}</td>
                        <td class="intervention-handle">☰ {{ $intervention->name }}</td>
                        <td>{{ $intervention->place->name }}</td>
                        <td>{{ $session->position }}</td>
                        <td>{{ $intervention->position }}</td>
                        <td>
                            @if($intervention->preferred_start_time)
                                {{ $intervention->preferred_start_time->format('H:i') }}
                            @endif
                        </td>
                        <td>{{ $interventionStartTime->format('H:i') }}</td>
                        <td>{{ $interventionEndTime->format('H:i') }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
        </tbody>
    </table>


</div>



@pushonce('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js" integrity="sha512-Eezs+g9Lq4TCCq0wae01s9PuNWzHYoCMkE97e2qdkYthpI0pzC3UGB03lgEHn2XM85hDOUF6qgqqszs+iXU4UA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
  const sortableContainer = $('#sortable');
  new Sortable(sortableContainer.get(0), {
    handle: '.session-handle',
    onEnd: function(evt) {
      if (evt.oldIndex !== evt.newIndex) {
        // The item's position actually changed
        console.log("Item moved from position", evt.oldIndex, "to position", evt.newIndex);

        // Now you can trigger any custom event or AJAX call here
      }
    }
  });


</script>
@endpush

