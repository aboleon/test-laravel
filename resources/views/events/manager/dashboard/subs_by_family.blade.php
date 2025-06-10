<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3">
        <h4>Inscriptions</h4>
    </header>
    <table class="table table-compact">
        <tbody>
        <tr>
            <th width="30%">Total</th>
            <td class="text-end pe-0 pe-sm-5">
                {{ collect($eventContactCountByServiceFamily['data'])->sum('contact_count') }}
            </td>
        </tr>
        @foreach($eventContactCountByServiceFamily['data'] as $family)
            @php
                $name = json_decode($family->name, true);
                $displayName = $name['fr'] ?? 'N/A';

                // Skip if no contacts and not active
                if ($family->contact_count == 0 && $family->is_active == 0) {
                    continue;
                }

                // Set background color for inactive families with contacts
                $bgClass = ($family->contact_count > 0 && $family->is_active == 0) ? 'bg-dark-subtle' : '';
            @endphp
            <tr>
                <th class="{{ $bgClass }}" width="30%" style="padding-left: 20px;">- {!! $displayName .( $bgClass ? ' <span style="font-weight:normal"> - inactif</span>' :'' ) !!} </th>
                <td class="{{ $bgClass }} text-end pe-0 pe-sm-5">
                    {{ $family->contact_count }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
