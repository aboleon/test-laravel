@php use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables; @endphp
<div class="wg-card">
    <header class="mb-3">
        <h4>Prestations choix</h4>
    </header>

    @php
        $nbChoosables = EventContactSellableServiceChoosables::getEventContactChosenChoosables($eventContact)->count();
    @endphp
    @if($nbChoosables)
        <div class="datatable-not-clickable">
            {!! $choosableDataTable->table()  !!}
        </div>
        @push('js')
            {{ $choosableDataTable->scripts() }}
        @endpush
    @else
        <p>Pas de prestation choix pour ce participant</p>
    @endif

</div>
