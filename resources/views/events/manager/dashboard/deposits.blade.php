<div class="col-md-6">
    <div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded">
        <header class="mfw-line-separator mb-3">
            <span class="mfw-badge mfw-bg-red float-end" style="padding: 5px 10px 3px 11px;">{{ $event->sellableServicesWithDeposit->count() }}</span>
            <h4>Cautions</h4>
        </header>
        @if ($event->sellableServicesWithDeposit->isNotEmpty())
            <table class="table table-compact">
                <thead>
                <tr>
                    <th>Montant</th>
                    <th>Prestation</th>
                    <th></th>
                </tr>
                </thead>
                @foreach($event->sellableServicesWithDeposit as $item)
                    <tr>
                        <td>
                            {{ $item->deposit->amount }} €
                        </td>
                        <td>
                            {{ $item->title }}
                        </td>
                        <td>
                            <ul class="mfw-actions">
                                <x-mfw::edit-link :route="route('panel.manager.event.sellable.edit', ['event'=>$event, 'sellable' => $item])"/>
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>
