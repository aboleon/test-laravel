<ul class="mfw-actions">
    <li class="delete action-dissociate-member-from-group"
        data-id="{{$data->id}}">
        <a href="#"
           class="dissociate btn btn-sm btn-warning"
           data-bs-placement="top"
           data-bs-title="{{__('front/groups.datatable_action_dissociate')}}"
           data-bs-toggle="tooltip">
            <i class="fas fa-unlink text-black"></i>
        </a>
    </li>
    <li class=""
        data-id="{{$data->id}}">
        <a href="{{route('front.event.switch-to-group-member', [
                'event' => $event->id,
                'group' => $data->group_id,
                'user' => $data->user_id,
                'routeType' => 'general-info',
            ])}}"
           class="btn btn-sm btn-info"
           data-bs-placement="top"
           data-bs-title="{{__('front/groups.datatable_action_edit')}}"
           data-bs-toggle="tooltip">
            <i class="bi bi-pencil-square text-white"></i>
        </a>
    </li>
</ul>
