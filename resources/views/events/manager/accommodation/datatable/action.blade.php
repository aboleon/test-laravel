<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.accommodation.show', ['event'=>$data->event_id, 'accommodation' => $data->id])"/>

    <x-mfw::delete-modal-link reference="{{ $data->id }}" title="Dissocier"/>
    <x-mfw::modal :route="route('panel.manager.event.accommodation.destroy', [
        'event' => $event,
        'accommodation' => $data->id,
    ])"
                  question="Dissocier l'hôtel {{ $data->name }} de l'événement ?"
                  reference="destroy_{{ $data->id }}"/>
</ul>
