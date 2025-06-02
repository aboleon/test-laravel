<x-front-logged-in-group-manager-v2-layout :event="$event">
    <h3 class="main-title">Commandes / Factures</h3>
    <div class="container front-datatable mt-5 datatable-not-clickable">
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-front-logged-in-group-manager-v2-layout>
