<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.sellable.edit', ['event'=>$data->event_id, 'sellable' => $data->id])"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.manager.event.sellable.destroy', ['sellable'=> $data->id, 'event' => $data->event_id])"
              title="Suppression d'une prestation"
              question="Supprimer la prestation <b>{{ $data->title }}</b> ?"
              reference="destroy_{{ $data->id }}"/>
