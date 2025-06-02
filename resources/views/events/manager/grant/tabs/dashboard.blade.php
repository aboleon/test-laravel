<div class="tab-pane fade show active"
     id="dashboard-tabpane"
     role="tabpanel"
     aria-labelledby="dashboard-tabpane-tab">

    {!! $recap->table() !!}

    @include('lib.datatable',['disable_DTclickableRow' => true])
    @push('js')
        {!! $recap->scripts() !!}
    @endpush
</div>
