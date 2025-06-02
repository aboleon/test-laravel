<ul class="mfw-actions d-flex gap-1">
    <x-mfw::edit-link :route="route('panel.manager.event.event_contact.edit', [
    'event' => $event->id,
    'event_contact' => $data->id,
     ])"/>
    @if (!$data->has_something)
        <x-mfw::delete-modal-link reference="{{ $data->id }}" title="Dissocier"/>
        <x-mfw::modal :route="route('panel.manager.event.event_contact.destroy', [
        'event' => $event,
        'event_contact' => $data->id,
        'group' => $group,
    ])"
                      question="Dissocier le contact {{ $data->first_name . ' '. $data->last_name }} de l'événement ?"
                      reference="destroy_{{ $data->id }}"/>
    @endif

    @if($data->nb_orders)
        <li>
            <a href="#" class="mfw-edit-link btn btn-sm btn-info"
               data-bs-toggle="modal"
               data-bs-target="#send_event_contact_confirmation_{{ $data->id }}"><i class="fas fa-file-pdf"></i></a>
        </li>
    @endif

    @if ($withLastGrantNotNull)
        @php
        /**
        * todo: currently idle, ask project manager what she wants to do with this button
        */
        @endphp
        <button class="btn btn-sm btn-yellow">Export PEC</button>
    @endif
</ul>

@if($data->nb_orders)
    <x-mfw::modal :route="route('panel.mailer', ['type' => 'sendEventContactConfirmation', 'identifier' => $data->uuid])"
                  title="Envoyer la confirmation par email ?"
                  :params="['uuid' => $data->uuid]"
                  class="send_event_contact_confirmation"
                  question="Envoyer le PDF de confirmation à l'adresse e-mail du contact."
                  reference="send_event_contact_confirmation_{{ $data->id }}"/>
@endif
