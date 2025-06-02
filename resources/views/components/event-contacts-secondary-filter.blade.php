<template id="eventContactsSecondaryFilterDropdown">
    <div class="dropdown" id="eventContactsSecondaryFilterBtn">
        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
           {{ $secondaryFilter ? \App\Enum\SecondaryEventContactFilter::translated($secondaryFilter) : 'Accès rapides' }}
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ $route }}">Voir tous les résultats</a></li>
            @foreach(\App\Enum\SecondaryEventContactFilter::keys() as $key)
                <li><a class="dropdown-item"
                       href="{{ $route . '?secondaryFilter='.$key }}">{{ \App\Enum\SecondaryEventContactFilter::translated($key) }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</template>
<style>
    #eventContactsSecondaryFilterBtn .dropdown-menu > li > a {
        padding: 5px;
        font-size: 14px;
    }
</style>
@push('callbacks')
    <script>

        function DTManipulations() {
            document.addEventListener('DOMContentLoaded', function () {
                let DTModal = new bootstrap.Modal(document.getElementById('DT-DeleteModal'), {
                    backdrop: true
                });

                let DTSelectAll = $('#datatable-select-all'),
                    DTC = $('#DT-container');

                $('.row-checkbox').off().on('click', function () {
                    console.log('row-checkbox');
                    $('.row-checkbox:checked').length ? DTC.removeClass('d-none') : DTC.addClass('d-none');
                });
                let DTDeleteBtn = $('#datatable-delete-selected');
                DTDeleteBtn.off();

                DTSelectAll.off().on('click', function () {
                    let checked = $(this).is(':checked');
                    checked ? DTC.removeClass('d-none') : DTC.addClass('d-none');
                    $('.row-checkbox').prop('checked', checked);
                });

                // Handle the "Delete Selected" button click
                DTDeleteBtn.off().on('click', function () {
                    let ids = [];

                    $('.row-checkbox:checked').each(function () {
                        ids.push($(this).val());
                    });

                    if (ids.length > 0) {
                        DTModal.show();
                        $('#DT-DeleteModalSave').off().on('click', function () {
                            let paramString = 'action=datatableMassDelete&model=' + DTDeleteBtn.data('model') + '&name=' + DTDeleteBtn.data('name') + '&ids=' + ids + '&callback=DTRedraw';
                            let controllerPath = DTDeleteBtn.data('controllerPath');
                            if ('' !== controllerPath) {
                                paramString += '&controller_path=' + controllerPath;
                            }
                            let modelPath = DTDeleteBtn.data('modelPath');
                            if ('' !== modelPath) {
                                paramString += '&model_path=' + modelPath;
                            }
                            let deletedMessage = DTDeleteBtn.data('deletedMessage');
                            if ('' !== deletedMessage) {
                                paramString += '&deleted_message=' + deletedMessage;
                            }
                            $.when(ajax(paramString, $('#DT-messages'))).then(DTModal.hide());

                        });
                    } else {
                        alert('Aucune ligne n\'a été sélectionnée');
                    }
                });
            });
        }

        function DTRedraw() {
            $('.messages').not('#DT-messages .messages').html('');
            $('.dt').DataTable().ajax.reload();
            DTManipulations();
            DTclickableRow();
        }
    </script>
@endpush

@push('js')

    <script>

        function eventContactsSecondaryFilterInit() {

            setTimeout(function () {

                let lastCell = $('#event_contact-table_wrapper .row:first > div:last-of-type');
                console.log(lastCell, 'lastCell');
                lastCell.addClass('d-flex justify-content-end xaxo');
                lastCell.prepend($('#eventContactsSecondaryFilterDropdown').html());


            }, 1500);
        }

        eventContactsSecondaryFilterInit();
    </script>
@endpush
