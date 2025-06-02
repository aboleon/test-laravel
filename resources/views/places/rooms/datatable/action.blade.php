<ul class="mfw-actions">
    <x-mfw::edit-link :route="route('panel.rooms.edit', $data)"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.rooms.destroy', $data)"
              title="{{__('ui.delete')}}"
              question="{!! __('ui.rooms.delete') !!} {{ $data->id . ' '. $data->name }} ?"
              reference="destroy_{{ $data->id }}"/>
