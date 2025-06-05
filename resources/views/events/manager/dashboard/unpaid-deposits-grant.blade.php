<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3">
        <h4>PEC attente de paiement de caution</h4>
    </header>
    <table class="table table-compact">
        <tbody>
        <tr>
            <th>Total Inscrits</th>
            <td>
                @php
                    $totalRow = collect($unpaidDepositsStats['data'])->firstWhere('participation_group', 'all');
                @endphp
                {{ $totalRow ? $totalRow->total : 0 }}
            </td>
        </tr>
        @foreach($unpaidDepositsStats['groups'] as $group)
            <tr>
                <th style="padding-left: 20px;">- {{ \App\Enum\ParticipantType::translated($group) }}</th>
                <td>
                    @php
                        $groupRow = $instance->filterByGroup($unpaidDepositsStats['data'], $group);
                    @endphp
                    {{ $groupRow ? $groupRow->total : 0 }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
