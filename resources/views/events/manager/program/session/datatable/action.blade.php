<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.program.session.edit', [
        'event'=> $event->id,
        'session' => $data->id,
    ])"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.manager.event.program.session.destroy', [
        'session' => $data,
        'event' => $event,
         ])"
              title="Suppression d'une session"
              question="Supprimer la session <b>{{ $data->name }}</b> ?"
              reference="destroy_{{ $data->id }}"/>
