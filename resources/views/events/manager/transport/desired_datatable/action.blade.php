<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.transport.edit', [
        'event'=> $event->id,
        'transport' => $data->id,
    ])" />

    @if('eventContactDashboard' !== $target)
        <x-mfw::delete-modal-link reference="{{ $data->id }}" />
    @endif
</ul>
@if('eventContactDashboard' !== $target)
    <x-mfw::modal :route="route('panel.manager.event.transport.destroy', [
        'event' => $event,
        'transport' => $data,
         ])"
                  title="Suppression d'un transport"
                  question="Supprimer ce transport <b>{{ $data->id }}</b> ?"
                  reference="destroy_{{ $data->id }}" />
@endif