<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.manager.event.grants.edit', ['event'=>$data->event_id, 'grant' => $data->id])" />
    @if(!$data->is_grant_consumed && !$data->allocations)
        <x-mfw::delete-modal-link reference="{{ $data->id }}" />
    @endif
</ul>
<x-mfw::modal :route="route('panel.manager.event.grants.destroy', ['grant'=>$data->id, 'event' => $data->event_id])"
              title="Suppression d'une prestation"
              question="Supprimer le grant ?"
              reference="destroy_{{ $data->id }}" />
