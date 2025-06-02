<div class="col">
    <div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded">
        <header class="mfw-line-separator mb-3">
            <span class="mfw-badge mfw-bg-red float-end" style="padding: 5px 10px 3px 11px;">{{ $event->sellable_service_count }}</span>
            <h4>Prestations</h4>
        </header>
        @if ($event->sellable_service_count > 0)
            <table class="table table-compact">
                <thead>
                <tr>
                    <th>Prestation</th>
                    <th>Prix</th>
                    <th>En ligne</th>
                    <th></th>
                </tr>
                </thead>
                @foreach($event->sellableService->load('deposit','prices') as $item)
                    <tr>
                        <td>
                            {{ $item->title }}
                        </td>
                        <td>
                            {{ $item->prices->count() > 1 ? $item->prices->min('price') .'-' . $item->prices->max('price') : $item->price }} €
                        </td>
                        <td>
                            {!! $item->isActive() !!}
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
        <a href="{{ route('panel.manager.event.sellable.index', $event) }}" class="btn btn-secondary btn-sm">Gestion</a>
        <a href="{{ route('panel.manager.event.sellable.create', $event) }}" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Créer</a>
    </div>
</div>
