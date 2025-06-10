@props([
        //public readonly string $title,
        'title' => '',
        //public readonly array $stats,
        'stats' => []
])
<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3">
        <h4>{{ $title }}</h4>
    </header>
    @if (!isset($stats['data']) or !isset($stats['groups']))
        <x-mfw::alert message="Données absentes"/>
    @else
        <table class="table table-compact">
            <tbody>
            <tr>
                <th width="30%">Total Inscrits</th>
                <td class="text-end pe-0 pe-sm-5">
                    @php
                        $totalRow = collect($stats['data'])->firstWhere('participation_group', 'all');
                    @endphp
                    {{ $totalRow ? $totalRow->total : 0 }}
                </td>
            </tr>
            @foreach($stats['groups'] as $group)
                <tr>
                    <th width="30%" style="padding-left: 20px;">- {{ \App\Enum\ParticipantType::translated($group) }}</th>
                    <td class="text-end pe-0 pe-sm-5">
                        @php
                            $groupRow = collect($stats['data'])->firstWhere('participation_group', $group);
                        @endphp
                        {{ $groupRow ? $groupRow->total : 0 }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
