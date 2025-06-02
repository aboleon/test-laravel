<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.establishments.edit', $data)"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.establishments.destroy', $data->id)"
                        title="{{__('ui.delete')}}"
                        question="{!! __('ui.establishments.delete') !!} {{ $data->id . ' '. $data->name }} ?"
                        reference="destroy_{{ $data->id }}"/>
