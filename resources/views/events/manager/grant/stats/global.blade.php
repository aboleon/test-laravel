@php
    use App\Services\Pec\PecType;

    global $totals;

    $totals = [
        'total' => 0,
        'sub_ht' => 0,
        'sub_vat' => 0
    ];

    $statsSummary = collect($statsSummary)->groupBy('type')->map(function ($group) {
        return [
            'total_amount' => $group->sum('total_amount'),
            'total_sub_ht' => $group->sum('total_sub_ht'),
            'total_sub_vat' => $group->sum('total_sub_vat'),
            'total_amount_formatted' => number_format($group->sum('total_amount') / 100, 2),
            'total_sub_ht_formatted' => number_format($group->sum('total_sub_ht') / 100, 2),
            'total_sub_vat_formatted' => number_format($group->sum('total_sub_vat') / 100, 2),
        ];
    });
@endphp
<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded">
    <header class="mfw-line-separator mb-3">
        <h4>Statistiques globales congrès</h4>
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
        <tbody>
        @foreach($stats->globalPecDistributionStatPosts() as $item)
            @php
                $stat = $statsSummary->get($item);
            @endphp
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
    </table>
</div>
