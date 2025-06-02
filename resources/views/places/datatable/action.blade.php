<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.places.edit', $data)"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.places.destroy', $data->id)"
                        title="{{__('ui.delete')}}"
                        question="{!! __('ui.places.delete') !!} {{ $data->id . ' '. $data->name }} ?"
                        reference="destroy_{{ $data->id }}"/>
