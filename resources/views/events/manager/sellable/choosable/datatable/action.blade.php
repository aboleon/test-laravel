<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.choosable.edit', ['event'=>$data->event_id, 'choosable' => $data->id])"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.manager.event.choosable.destroy', ['choosable'=>$data, 'event' => $data->event_id])"
              title="Suppression d'une prestation au choix"
              question="Supprimer la prestation <b>{{ $data->title }}</b> ?"
              reference="destroy_{{ $data->id }}"/>
