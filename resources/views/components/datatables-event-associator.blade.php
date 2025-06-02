<style>
    #DT-event-associator {
        display: flex;
        white-space: nowrap;
        align-items: center;
    }

    #DT-event-associator label {
        margin: 0 8px 0;
    }
</style>
@pushonce('css')
    {!! csscrush_tag(public_path('vendor/mfw/css/fragments/_dynamic_search.css')) !!}
@endpushonce
<div id="event-associator-model-container" class="d-inline-block me-2 d-none" data-type="{{ $type }}">
    <x-mfw::simple-modal id="event-associator-model" body="Associer les éléménts sélectionnés à un évènement ?"
                         text="Associer à un évènement" title="Associer à un évènement" class="btn btn-secondary btn-sm"
                         callback="eventAssociatorJs" confirm="Associer"/>
</div>

<template id="events_selectable">
    <div id="events_select_modal" class="mt-2">
        <x-mfw::select name="event_selectables" :values="\App\Accessors\EventAccessor::eventsArray()" :affected="null"
                       label="Liste des évènements" :nullable="false"/>
    </div>
</template>

<template id="template-dt-event-associator-messages">
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
                    modal = document.getElementById('mfw-simple-modal');
                modalInstance = bootstrap.Modal.getInstance(modal);

                $('.row-checkbox:checked').each(function () {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {

                    let formData = 'action=eventAssociator&event_id=' + $('#event_selectables').val() + '&ids=' + ids + '&callback=DTRedraw&type=' + $('#event-associator-model-container').data('type');
                    $.when(ajax(formData, $('#DT-event-associator-messages'))).then(modalInstance.hide());
                    DTEventAssociatorRedraw();
                }
            });
        }

        function DTEventAssociatorRedraw() {
            $('.messages').not('#DT-event-associator-messages .messages').html('');
            $('.dt').DataTable().ajax.reload();
        }
    </script>
@endpush

@push('js')

    <script>
        setTimeout(function () {
            let et = $('.dataTables_wrapper'),
                et_row_first = et.find('.row:first');
            et_row_first.find('> div:first-of-type').removeClass('col-md-6').addClass('col-md-2');
            et_row_first.find('> div:last-of-type').removeClass('col-md-6').addClass('d-flex justify-content-end col-md-10');
            $($('#event-associator-model-container')).appendTo($('#DT-container')).removeClass('d-none');
            $($('#template-dt-event-associator-messages').html()).insertBefore(et.find('.dt-row'));
        }, 1000);
    </script>
@endpush
