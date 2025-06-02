<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3">
        <h4>Participants</h4>
    </header>
    <table class="table table-compact">
        <tbody>
        <tr>
            <th>Total Potentiel</th>
            <td>{{ $event->contacts()->count() }}</td>
        </tr>
        <tr>
            <th>Total Inscrits</th>
            <td>{{ $event->contacts()->whereHas('orders', fn($q) => $q->where('event_id', $event->id))->count() }}</td>
        </tr>
        </tbody>
    </table>
</div>
