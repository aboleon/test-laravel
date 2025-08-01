<table class="table">
    <thead>
    <tr>
        <th>Catégorie</th>
        <th>Date</th>
        <th>Contingent hôtel</th>
        <th>Bloquées</th>
        <th>Réservées sur le quota bloqué</th>
        <th>Restantes sur le quota bloqué</th>
        <th>Réservées en libre</th>
    </tr>
    </thead>
    <tbody>

    @php
        $total_booked = 0;
        $total_blocked = 0;
        $availability_recap = (new \App\Accessors\EventManager\Availability\AvailabilityRecap($availability));
        $blocked_recap = (new \App\Accessors\EventManager\Availability\Blocked($availability));
    @endphp

    @foreach($availability->get('contingent') as $date => $contingent)

        @foreach($contingent as $roomgroup => $total)
            @php
                $recap = $availability_recap->get($date, $roomgroup);
                $blocked = $blocked_recap->get($date, $roomgroup);
                $row_id = Str::random();
                $rowspan = count($contingent);
                $iteration = $loop->index;
                $blocked_count = $group_id ? ($blocked['groups_event_group_id'][$group_id] ?? 0) : array_sum($blocked['groups_event_group_id']);

                // FIXED: Added safe access with default values
                $confirmed_groups_on_quota = $recap['confirmed']['groups']['on_quota'] ?? 0;
                $temp_groups_on_quota = $recap['temp']['groups']['on_quota'] ?? 0;
                $confirmed_groups_free = $recap['confirmed']['groups']['free'] ?? 0;
                $temp_groups_free = $recap['temp']['groups']['free'] ?? 0;

                $total_booked += $confirmed_groups_on_quota + $temp_groups_on_quota;
                $total_blocked += $blocked_count;
                $tempbooked = ($availability->get('booked')['temp'][$date][$roomgroup] ?? 0);
            @endphp

            <tr class="contingent-row {{ $row_id }}">
                <td>
                    {{-- Catégorie --}}
                    #{{ $roomgroup }}
                    {{ $availability->getRoomGroup($roomgroup)['name'] }}
                </td>
                @if($iteration < 1)
                    {{-- Date --}}
                    <td class="rowspan align-top" rowspan="{{ $rowspan }}" style="max-width: 160px">
                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}
                    </td>
                @endif
                <td class="rowspan align-top" style="max-width: 80px">
                    {{-- Contingent --}}
                    {{ $total }}
                </td>
                <td class="rowspan align-top" style="max-width: 80px">
                    {{-- Bloquées --}}
                    {{ $blocked_count }}
                </td>
                <td class="rowspan align-top" style="max-width: 80px">
                    {{-- Réservées sur le quota bloqué --}}
                    {{ $confirmed_groups_on_quota }}
                    @if ($temp_groups_on_quota)
                        (+ {{ $temp_groups_on_quota }} en attente)
                    @endif
                </td>
                <td class="rowspan align-top" style="max-width: 80px">
                    {{-- Restantes sur le quota bloqué --}}
                    {{ $blocked_count - ($confirmed_groups_on_quota + $temp_groups_on_quota)}}
                </td>
                <td>
                    {{-- Réservées en libre --}}
                    {{-- FIXED: Corrected typo from $recap$re to $recap --}}
                    {{ $confirmed_groups_free + $temp_groups_free }}
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
    <tfoot class="d-none" data-delta="{{ $total_blocked - $total_booked }}" data-blocked="{{ $total_blocked }}"
           data-booked="{{ $total_booked }}"></tfoot>
</table>

{{-- d($availability->getSummarizedData()) --}}
