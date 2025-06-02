<div class="wg-card">
    <header class="mb-3">
            <span class="mfw-badge mfw-bg-red float-end"
                  style="padding: 5px 10px 3px 11px;">{{ $orders->count() }}</span>
        <h4>Attributions</h4>
    </header>

    @if ($attributed->isNotEmpty())

        <table class="table table-hover">
            <thead>
            <th>Type</th>
            <th>Libellé</th>
            <th>Contenu</th>
            <th>Origine</th>
            <th>Actions</th>
            </thead>
            <tbody>

            @foreach($attributed as $item)
                <tr>
                    <td>{{ \App\Enum\OrderCartType::translated($item['type']) }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>{!!  $item['text']  !!}</td>
                    <td>{{ $item['attributed'] }}</td>
                    <td>

                        <ul class="mfw-actions">
                            <x-mfw::edit-link
                                :route="route('panel.manager.event.orders.edit', ['event' => $item['event_id'], 'order' => $item['order_id']])"/>
                            <li>
                                <a class="mfw-edit-link btn btn-sm btn-success pe-1" target="_blank"
                                   data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Gérer les attributions"
                                   href="{{ route('panel.manager.event.orders.attributions', ['event' => $item['event_id'], 'order' => $item['order_id'], 'type'=> $item['type']]) }}"><i
                                        class="bi bi-boxes me-1"></i></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @else
        <x-mfw::alert type="warning" message="Aucune attribution pour ce compte client" />
    @endif

    <div class="mfw-line-separator my-3"></div>

</div>
