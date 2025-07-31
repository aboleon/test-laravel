<ul class="mfw-actions">
    @if (is_null($data->deleted_at))
        <x-dashboard-link :route="route('panel.manager.event.show', $data->id)"/>
    @endif
    <x-mfw::edit-link :route="route('panel.events.edit', $data->id)"/>

    @if (!is_null($data->deleted_at))
        <x-mfw::restore-modal-link reference="{{ $data->id }}"/>
    @else
        <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
    @endif
</ul>
@if (!is_null($data->deleted_at))
    <x-mfw::modal :route="route('panel.events.restore', $data)"
                  question="Rétablir cet évènement ?"
                  title="Gestion des évènements"
                  reference="restore_{{ $data->id }}"/>
@else
    <x-mfw::modal :route="route('panel.events.destroy', $data->id)"
                  title="{{__('ui.delete')}}"
                  question="{!! __('ui.events.delete') !!} {{ $data->id . ' '. $data->name }} ?"
                  reference="destroy_{{ $data->id }}"/>

@endif
