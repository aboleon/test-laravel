<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.event_group.edit', [
    'event' => $event->id,
    'event_group' => $data->id,
     ])"/>
    @if (!$data->orders_count)
        <x-mfw::delete-modal-link reference="{{ $data->id }}" title="Dissocier"/>
        <x-mfw::modal :route="route('panel.manager.event.event_group.destroy', [
        'event' => $event,
        'event_group' => $data->id,
    ])"
                      question="Dissocier le group {{ $data->name }} de l'événement ?"
                      reference="destroy_{{ $data->id }}"/>
    @endif
</ul>
