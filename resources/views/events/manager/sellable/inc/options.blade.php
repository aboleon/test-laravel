<h4>Options</h4>

<div id="sellable-service-options">
    @foreach($data->options as $option)
        <x-event-sellable-option-row :option="$option"/>
    @endforeach
</div>
<button class="btn btn-sm btn-success mt-3" id="add-sellable-service-option" type="button">
    <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
</button>
<div id="sellable_service_option_messages" data-ajax="{{ route('ajax') }}"></div>
<template id="sellable-service-option-row">
    <x-event-sellable-option-row :option="new \App\Models\EventManager\Sellable\Option()"/>
</template>
@push('callbacks')
    <script>
        function ajaxPostDeleteOption(result) {
            $(result.input.identifier).remove();
        }
    </script>
@endpush
@push('js')
    <script>
        function deleteSellableOption() {
            $('.delete_sellable_service_option').off().on('click', function () {

                $('.messages').html('');
                let id = $(this).attr('data-model-id'),
                    identifier = '.sellable-service-option-row[data-identifier=' + $(this).attr('data-identifier') + ']';
                $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                console.log(id, identifier, (id.length < 1 || isNaN(id)));
                if (id.length < 1 || isNaN(id)) {
                    $(identifier).remove();
                } else {
                    ajax('action=removeSellableServiceOptionRow&id=' + Number(id) + '&identifier=' + identifier, $('#sellable_service_option_messages'));
                }
            });
        }
    </script>
@endpush
