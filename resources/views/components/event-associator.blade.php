<div id="event-associator-model-container" class="d-inline-block mx-2" data-type="{{ $type }}" data-id="{{ $id }}">
    <x-mfw::simple-modal id="event-associator-model" text="Association à un évènement"
                         title="Association à un évènement" class="btn btn-secondary btn-sm"
                         callback="eventAssociatorJs" confirm="Associer"/>
</div>

<template id="events_selectable">
    <div id="events_select_modal" class="mt-2">
        <x-mfw::select name="event_selectables" :values="\App\Accessors\EventAccessor::eventsArray()" :affected="null"
                       label="Liste des évènements" :nullable="false"/>
    </div>
</template>

<template id="template-event-associator-messages">
    <div class="row">
        <div id="DT-event-associator-messages" class="col" data-ajax="{{ route('ajax') }}"></div>
    </div>
</template>
@push('callbacks')
    <script>
        function eventAssociatorJs() {
            $($('#events_selectable').html()).appendTo($('#mfw-simple-modal .modal-body'));
            $('button.event-associator-model').off().click(function () {
                let ids = [],
                    c = $('#event-associator-model-container'),
                    modal = document.getElementById('mfw-simple-modal');
                modalInstance = bootstrap.Modal.getInstance(modal);

                ids.push(c.data('id'));

                let formData = 'action=eventAssociator&event_id=' + $('#event_selectables').val() + '&ids=' + ids + '&callback=eventAssociatorCallback&type=' + c.data('type');
                $.when(ajax(formData, $('#DT-event-associator-messages'))).then(modalInstance.hide());
            });
        }
    </script>
@endpush
@push('js')

    <script>
        function eventAssociatorCallback() {
            $('.messages').not('#DT-event-associator-messages .messages').html('');
        }

        setTimeout(function () {
            $($('#template-event-associator-messages').html()).insertBefore($("#main"));
        }, 1000);
    </script>
@endpush
