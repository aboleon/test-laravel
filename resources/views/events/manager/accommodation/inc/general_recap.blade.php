<table class="table">
    <thead>
    <tr>
        <td colspan="9" class="text-danger fw-bold">
            * dont réservations issues des quotas bloquées
        </td>
        <th colspan="4" class="bg-body-secondary text-center">GRANT</th>
    </tr>
    <tr>
        <th>Date</th>
        <th>Catégorie</th>
        <th class="text-end">Contigent</th>
        <th class="text-end">Réservations</th>
        <th class="text-end">En attente</th>
        <th class="text-end">Bloquées</th>
        <th class="text-end">Restant</th>
        <th class="text-end">Annulées</th>
        <th class="text-end">Modifiées</th>
        <th class="text-end bg-body-tertiary">Résas</th>
        <th class="text-end bg-body-tertiary">En attente</th>
        <th class="text-end bg-body-tertiary">Bloquées</th>
        <th class="text-end bg-body-tertiary">Restant</th>
    </tr>
    </thead>
    <tbody>
    @php
        $getAvailability = $availability->getAvailability();

       //d($availability->getAvailability());
        $availability_recap = (new \App\Accessors\EventManager\Availability\AvailabilityRecap($availability));

        $_REQUEST['GLOBALS']['contingent_dates'] = $availability->get('contingent')
        ? collect([array_key_first($availability->get('contingent')), array_key_last($availability->get('contingent'))])->map(fn($item) => \Carbon\Carbon::createFromFormat('Y-m-d', $item)->format('d/m/Y'))
        : collect();



    @endphp


    @foreach($availability->get('contingent') as $date => $contingent)

        @php
            $totalContingentForDate = array_sum($contingent);
            $cumultatedAvailable = array_sum($getAvailability[$date])
        @endphp

        @foreach($contingent as $roomgroup => $total)
            @php
                $row_id = Str::random();
                $rowspan = count($contingent);
                $iteration = $loop->index;
                $recap = $availability_recap->get($date, $roomgroup);

                $from_blocked = $recap['confirmed']['total_individual_quota']
                + $recap['confirmed']['total_groups_quota']
                + $recap['temp']['total_individual_quota']
                + $recap['temp']['total_groups_quota']
                + $recap['pec']['to_add']
                - $recap['pec']['to_substract_from_blocked_ptype']
                ;

                $blocked_grants = $availability->get('blocked.grants.'.$date.'.'.$roomgroup, 0);
                $distributed_grants = $availability->get('booked.grants.'.$date.'.'.$roomgroup, 0);
                $cumultatedAvailable = 0;
                $temp_pec = $recap['temp']['total_pec'];

                /* Control Debug
                if ($date == '2025-03-03' && $roomgroup == 68) {
                    d($getAvailability,'$getAvailability');
                    d($recap, $date . ' - ' . $roomgroup);
                }
*/
            @endphp

            <tr class="contingent-row {{ $row_id }} stock_{{$roomgroup}} date_{{ $date }}"
                data-stock="{{ $getAvailability[$date][$roomgroup] }}">
                @if($iteration < 1)
                    <td class="rowspan align-top" rowspan="{{ $rowspan }}" style="max-width: 160px">
                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}
                    </td>
                @endif
                <td data-roomgroup="{{ $roomgroup }}">
                    {{ $availability->getRoomGroup($roomgroup)['name'] }}
                </td>
                <td class="is-available rowspan align-top text-end fw-bold" style="max-width: 80px">
                    {{ $total }}
                </td>
                <td class="is-confirmed rowspan align-top text-end fw-bold" style="max-width: 80px">
                    {{ $recap['confirmed']['total']  - $recap['cancelled']['total'] - $recap['amended']['total']}}
                    @if($from_blocked)
                        <span class="text-danger">{{ $from_blocked }}</span>
                    @endif
                </td>
                <td class="is-temp text-end fw-bold" style="opacity: .5">{{ $recap['temp']['total'] }}</td>
                <td class="is-blocked rowspan align-middle text-end fw-bold" style="max-width: 80px">
                    {{
                        $recap['blocked']['total'],
                    }}
                </td>
                <td class="is-remaining rowspan align-top text-end fw-bold" style="max-width: 80px">
                    {{ $getAvailability[$date][$roomgroup] }}
                </td>
                <td class="is-cancelled text-end text-secondary">
                    {{ $recap['cancelled']['by_date'][$date][$roomgroup]['total'] ?? 0 }}
                </td>
                <td class="is-amended text-end text-secondary">
                    {{ $recap['amended']['total'] }}
                </td>
                <td class="is-pec rowspan bg-body-tertiary align-top text-end text-secondary grant-booked-{{$date}}">
                    {{ $recap['confirmed']['total_pec'] }}
                </td>
                <td class="is-pec-temp rowspan bg-body-tertiary align-top text-end text-secondary grant-temp-{{$date}}">
                    {{ $recap['temp']['total_pec'] }}
                </td>
                <td class="is-pec-blocked align-middle bg-body-tertiary text-end text-secondary blocked-grant-{{$date}}"
                    data-blocked="{{ $blocked_grants }}">
                    {{ $blocked_grants }}
                </td>
                <td class="is-pec-blocked-remaining text-end bg-body-tertiary text-secondary grant-remaining-{{$date}}">
                    {{ ($distributed_grants >= $blocked_grants ? 0 : $blocked_grants - $distributed_grants - $temp_pec) }}
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
