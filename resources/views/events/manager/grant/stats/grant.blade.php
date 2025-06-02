@php
    use App\Services\Pec\PecType;

    global $totals;

    $totals = [
        'total' => 0,
        'sub_ht' => 0,
        'sub_vat' => 0,
    ];

@endphp
<style>
    .state.btn-success:hover {
        background: #48a97c;
        border: 1px solid #48a97c;
    }
</style>

<div class="wg-card dashboard-widget my-4 shadow p-4 bg-body-tertiary rounded">
    <header class="mfw-line-separator mb-3 d-flex justify-content-between align-items-center">
        <h4>{{ $grant->title }}</h4>
        <div>
            <a class="btn btn-sm btn-secondary"
               href="{{ route('panel.manager.event.grants.recap', [$event, $grant]) }}">Récap</a>
            <span
                style="cursor:default;"
                class="state btn btn-sm btn-{{ $grant->active ? 'success' : 'danger' }}">{{ $grant->active ? 'Activé' : 'Désactivé' }}</span>
        </div>
    </header>
    <table class="table table-compact">
        <thead>
        <tr>
            <th>Type</th>
            <th>Montant HT</th>
            <th>TVA</th>
            <th>Montant TTC</th>
        </tr>
        </thead>
        @if ($statsSummaryGrouped->has($grant->id))
            <tbody>

            @php
                $statsSummary = $statsSummaryGrouped[$grant->id];
            @endphp

            @foreach($statsSummary as $item => $stat)
                @include('events.manager.grant.stats.shared_output')
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Total</th>
                <th>{{ round($totals['sub_ht']/100, 2) }} €</th>
                <th>{{ round($totals['sub_vat']/100, 2) }} €</th>
                <th>{{ round($totals['total']/100, 2) }} €</th>

            </tr>
            </tfoot>
        @else
            <tr>
                <td colspan="4">Aucune utilisation</td>
            </tr>
        @endif
    </table>
</div>

