<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3 row">
        <div class="col-lg-8">
            <h4>Statistiques cautions grant</h4>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('panel.manager.event.event_deposit.index', $event) }}" class="btn btn-sm btn-danger">Voir
                les cautions</a>
        </div>
    </header>
    <table class="table table-compact">
        <thead>
        <tr>
            <th>Type</th>
            <th>Nombre</th>
            <th>Détail</th>
            <th>Montant TTC</th>
            <th>dont TVA</th>
        </tr>
        </thead>
        <tbody>
        @foreach($deposits->getDeposits() as $key => $item)
            <tr>
                <td>{{ $statusOrder[$key] }}</td>
                <td>{{$item['count']}}</td>
                <td>
                    <a href="{{route('panel.manager.event.event_deposit.index', ['event' => $event,'status' => $item['status']])}}"
                       class="mfw-edit-link btn btn-sm btn-secondary">
                        Voir
                    </a>
                </td>
                <td>{{ \MetaFramework\Accessors\Prices::readableFormat($item['total']) }}</td>
                <td>
                    @if ($key == \App\Enum\EventDepositStatus::BILLED->value)
                        {{ \MetaFramework\Accessors\Prices::readableFormat($item['total_vat']) }}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
