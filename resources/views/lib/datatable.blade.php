@push('css')
    <link href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.min.css"
          rel="stylesheet"/>
@endpush
@push('js')
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function reloadDatable() {
            $('.dt').DataTable().ajax.reload();
        }

        @if(!isset($disable_DTclickableRow))
        function DTclickableRow() {
            setTimeout(function () {
                $('.dt.dataTable tbody > tr > td:not(:nth-child(0)):not(:nth-child(1)):not(:last-of-type):not(.unclickable)').css('cursor', 'pointer').click(function () {
                    if (!$(this).closest('.datatable-not-clickable').length) {
                        window.location.assign($(this).parent().find('a.mfw-edit-link').attr('href'));
                    }
                });
            }, 1000);
        }


        DTclickableRow();

        $('.dt').on('draw.dt', function () {
            DTclickableRow();
        });
        @endif
    </script>
@endpush

