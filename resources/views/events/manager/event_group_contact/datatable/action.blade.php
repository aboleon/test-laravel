<ul class="mfw-actions">
    <li class="delete"
        data-bs-toggle="modal"
        data-bs-target="#dissociate_destroy_from_event_group_{{ $data->id }}">
        <a href="#" class="dissociate btn btn-sm btn-warning"
           style="color: #5b5b5b !important;"
           data-bs-placement="top" data-bs-title="Dissocier"
           data-bs-toggle="tooltip">
            <i class="fa-solid fa-link-slash"></i>
        </a>
    </li>
    <x-mfw::modal :route="route('panel.manager.event.event_group_contact.destroy', [
        'event' => $event,
        'event_group_contact' => $data->id,
    ])"
                  question="Dissocier le contact {{ $data->first_name . ' '. $data->last_name }} de ce groupe d'événement ?"
                  reference="dissociate_destroy_from_event_group_{{ $data->id }}"/>


</ul>
