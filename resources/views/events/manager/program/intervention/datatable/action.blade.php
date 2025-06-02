<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.program.intervention.edit', [
        'event'=> $event->id,
        'intervention' => $data->id,
        'session' => $sessionId
    ])"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.manager.event.program.intervention.destroy', [
        'event' => $event,
        'intervention' => $data,
        'session' => $sessionId
         ])"
              title="Suppression d'une intervention"
              question="Supprimer l'intervention <b>{{ $data->name }}</b> ?"
              reference="destroy_{{ $data->id }}"/>
