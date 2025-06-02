<div class="wg-card">
    {!! $pecDataTable->table()  !!}
    @push('js')
        {{ $pecDataTable->scripts() }}
    @endpush
</div>
@include('pec-orders.datatable.modal')
