<div class="wg-card">
    <header class="mb-3">
        <h4>Sessions</h4>
    </header>

    @if(true)
        {!! $sessionDataTable->table()  !!}
        @push('js')
            {{ $sessionDataTable->scripts() }}
        @endpush
    @else
        <p>Pas de sessions pour ce participant</p>
    @endif


    <div class="mfw-line-separator my-3"></div>
</div>
