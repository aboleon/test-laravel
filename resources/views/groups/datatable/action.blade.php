<ul class="mfw-actions">
    <li>
        <x-mfw::edit-link :route="route('panel.groups.edit', $data)"/>
        <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.groups.destroy', $data)"
                        title="{{__('ui.delete')}}"
                        question="Supprimer le groupe {{  $data->name .' / '. $data->company }} ?"
                        reference="destroy_{{ $data->id }}"/>
