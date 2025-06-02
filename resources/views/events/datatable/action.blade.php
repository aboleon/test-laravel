<ul class="mfw-actions">
    <x-dashboard-link :route="route('panel.manager.event.show', $data->id)"/>
    <x-mfw::edit-link :route="route('panel.events.edit', $data->id)"/>
    <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
</ul>
<x-mfw::modal :route="route('panel.events.destroy', $data->id)"
                        title="{{__('ui.delete')}}"
                        question="{!! __('ui.events.delete') !!} {{ $data->id . ' '. $data->name }} ?"
                        reference="destroy_{{ $data->id }}"/>
