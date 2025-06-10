<div class="modal fade"
     id="modal_export"
     tabindex="-1"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        @include('exports.global-export-ui', ['action' => $action, 'event' => $event])
    </div>
</div>

@push('js')
    <script>
        $('#modal_export').on('hidden.bs.modal', function (e) {
            $(this).find('.messages').html('');
            $(this).find('.mfw-spinner').addClass('d-none');
        });
    </script>
@endpush
