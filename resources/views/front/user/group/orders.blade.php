<x-front-logged-in-group-manager-layout :event="$event">
    <div class="container front-datatable mt-5 datatable-not-clickable">
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush
</x-front-logged-in-group-manager-layout>
