<div class="wg-card">
    <header class="mb-3">
        <h4>Interventions</h4>
    </header>



    @if(true)
        {!! $interventionDataTable->table()  !!}
        @push('js')
            {{ $interventionDataTable->scripts() }}
        @endpush
    @else
        <p>Pas d'interventions pour ce participant</p>
    @endif

    <div class="mfw-line-separator my-3"></div>
</div>
