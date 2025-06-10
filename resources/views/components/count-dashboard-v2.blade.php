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
    @if (!isset($stats['total']))
        <x-mfw::alert message="Données absentes"/>
    @else
        <table class="table table-compact">
            <tbody>
            <tr>
                <th width="30%">Total</th>
                <td class="text-end pe-0 pe-sm-5">
                    {{ $stats['total'] ?? 0 }}
                </td>
            </tr>
            </tbody>
        </table>
    @endif
</div>
