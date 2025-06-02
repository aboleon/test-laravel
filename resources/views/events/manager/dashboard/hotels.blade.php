<div class="wg-card dashboard-widget shadow p-4 bg-body-tertiary rounded mb-3">
    <header class="mfw-line-separator mb-3">
        <span class="mfw-badge mfw-bg-red float-end"
              style="padding: 5px 10px 3px 11px;">{{ $event->accommodation_count }}</span>
        <h4>Hébergements</h4>
    </header>
    @if ($event->accommodation->isNotEmpty())
        <table class="table table-compact">
            <thead>
            <tr>
                <th>Hôtel</th>
                <th>Ville</th>
                <th>En ligne</th>
                <th></th>
            </tr>
            </thead>
            @foreach($event->accommodation->load('hotel.address') as $item)
                <tr>
                    <td>
                        {{ $item->hotel->name }}
                    </td>
                    <td>
                        {{ $item->hotel->address?->locality ?? '' }}
                    </td>
                    <td>
                        {!! $item->isActive() !!}
                    </td>
                    <td>
                        <ul class="mfw-actions">
                            <x-mfw::edit-link
                                :route="route('panel.manager.event.accommodation.edit', ['event'=>$event, 'accommodation' => $item])"/>
                        </ul>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
    <a href="{{ route('panel.manager.event.accommodation.index', $event) }}"
       class="btn btn-secondary btn-sm">Gestion</a>
</div>
