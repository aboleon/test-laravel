<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3">
        <h4>Participants</h4>
    </header>
    <table class="table table-compact">
        <tbody>
        <tr>
            <th width="30%">Total Potentiel</th>
            <td class="text-end pe-0 pe-sm-5">{{ $event->contacts()->count() }}</td>
        </tr>
        <tr>
            <th width="30%">Total Inscrits</th>
            <td class="text-end pe-0 pe-sm-5">
                @php
                    $totalRow = collect($participantsStats['data'])->firstWhere('participation_group', 'all');
                @endphp
                {{ $totalRow ? $totalRow->total : 0 }}
            </td>
        </tr>
        @foreach($participantsStats['groups'] as $group)
            <tr>
                <th width="30%" style="padding-left: 20px;">- {{ \App\Enum\ParticipantType::translated($group) }}</th>
                <td class="text-end pe-0 pe-sm-5">
                    @php
                        $groupRow = $instance->filterByGroup($participantsStats['data'], $group);
                    @endphp
                    {{ $groupRow ? $groupRow->total : 0 }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
