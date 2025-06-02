@php

    global $totals;

    use App\Services\Pec\PecType;

    switch ($item) {
        case PecType::ACCOMMODATION->value:
            $taxrooms = $statsSummary->get(PecType::TAXROOM->value, [
                'total_amount' => 0,
                'total_sub_ht' => 0,
                'total_sub_vat' => 0,
                'total_amount_formatted' => '0.00',
                'total_sub_ht_formatted' => '0.00',
                'total_sub_vat_formatted' => '0.00',
            ]);

            $totals['total'] += ($stat['total_amount'] ?? 0) + ($taxrooms['total_amount'] ?? 0);
            $totals['sub_ht'] += ($stat['total_sub_ht'] ?? 0) + ($taxrooms['total_sub_ht'] ?? 0);
            $totals['sub_vat'] += ($stat['total_sub_vat'] ?? 0) + ($taxrooms['total_sub_vat'] ?? 0);
            break;
        default:
            $totals['total'] += $stat['total_amount'] ?? 0;
            $totals['sub_ht'] += $stat['total_sub_ht'] ?? 0;
            $totals['sub_vat'] += $stat['total_sub_vat'] ?? 0;
    }
@endphp
<tr>
    <td>Montant PEC {{ PecType::translated($item) }}</td>
    <td>{{ $stat['total_sub_ht_formatted'] ?? '0.00' }} €</td>
    <td>{{ $stat['total_sub_vat_formatted'] ?? '0.00' }} €</td>
    <td>{{ $stat['total_amount_formatted'] ?? '0.00' }} €</td>
</tr>
