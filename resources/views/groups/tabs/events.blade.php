<div class="tab-pane fade"
     id="events-tabpane"
     role="tabpanel"
     aria-labelledby="events-tab"
     data-ajax="{{route('ajax')}}"
>


    <div class="messages"></div>

    @if ($data->events->isNotEmpty())
        <table class="table event-group-event-table">
            <thead>
            <th>Évènement</th>
            <th>Contact Principal</th>
            <th class="text-end">Actions</th>
            </thead>
            @foreach($data->events->load('texts') as $event)
                <tr class="">
                    <td>
                        {!! $event->texts->name !!}
                    </td>
                    <td>
                        @php
                            $eventGroup = $event->eventGroups()->with('mainContact')->where('group_id', $data->id)->first();
                            if($eventGroup){
                                echo $eventGroup?->mainContact?->fullName();
                            }
                        @endphp
                    </td>
                    <td>
                        <ul class="mfw-actions">
                            @if($eventGroup)
                                <li>
                                    <a href="{{route('panel.manager.event.event_group.edit', [
                                        'event' => $event->id,
                                        'event_group' => $eventGroup->id,
                                    ])}}"
                                       class="btn btn-sm btn-info"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-bs-title="Voir le groupe participant">
                                        <i class="bi bi-people"></i>
                                    </a>
                                </li>
                            @endif
                            @if($eventGroup && $eventGroup->mainContact)
                                <li>
                                    <a href="#"
                                       class="btn btn-sm btn-yellow btn-send-mail"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-user-id="{{$eventGroup->mainContact->id}}"
                                       data-event-id="{{$event->id}}"
                                       data-group-id="{{$data->id}}"
                                       data-bs-title="Mail de connexion">
                                        <i class="bi bi-envelope"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
</div>

@push("js")
    <script>
      $(document).ready(function() {
        const jContext = $('#events-tabpane');
        $('.event-group-event-table').on('click', '.btn-send-mail', function() {
          let action = 'action=sendConnexionMailToEventGroupMainContact';
          action += '&user_id=' + $(this).data('user-id');
          action += '&event_id=' + $(this).data('event-id');
          action += '&group_id=' + $(this).data('group-id');

          ajax(action, jContext, {
            successHandler: function() {
              console.log('success');
              return true;
            },
          });
          return false;
        });
      });
    </script>
@endpush
