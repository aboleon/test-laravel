<div class="container mt-4 intervention-container">
    <div class="mb-3">
        <label>
            <input type="checkbox" id="toggleFixedInterventions">
            {{__('programs.hide_custom_interventions')}}
        </label>
    </div>

    <ul id="sortable">
        @foreach($program as $day)
            <li class="day">
                {{ $day->datetime_start->format('Y-m-d') }}
                <ul>
                    @php
                        $currentStartTime = clone $day->datetime_start;
                    @endphp

                    @foreach($day->sessions as $session)
                        <li class="session-handle">
                            <span>☰ {{ $session->name }}</span>
                            <ul>
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

                                    <li class="intervention @if($intervention->preferred_start_time) custom-intervention-row @endif">
                                        ☰ {{ $intervention->name }} |
                                        {{ $intervention->place->name }} |
                                        {{ $session->position }} |
                                        {{ $intervention->position }} |
                                        @if($intervention->preferred_start_time)
                                            {{ $intervention->preferred_start_time->format('H:i') }} |
                                        @endif
                                        {{ $interventionStartTime->format('H:i') }} |
                                        {{ $interventionEndTime->format('H:i') }}
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
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

    sort: true,
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

