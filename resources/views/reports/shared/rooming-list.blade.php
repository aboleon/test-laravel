<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        @if ($with_order ?? false)
            <th>Commande</th>
        @endif
        <th>Type de participation</th>
        <th>Nom</th>
        <th>Prenom</th>
        <th>Societe</th>
        <th>Pays</th>
        <th>Payeur</th>
        <th>PEC</th>
        <th>Email Participant</th>
        <th>Catégorie de chambre</th>
        <th>Type de chambre</th>
        <th>Commentaire</th>
        <th>Nombre de personnes</th>
        <th>Accompagnant(s)</th>
        <th>Check-in</th>
        <th>Check out</th>
        <th>Nb nuits</th>
        @foreach($roomingList->bookingsDates() as $date)
            <th>{{ Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}</th>
            <th>Prix</th>
        @endforeach
        <th>Total séjour</th>
        <th>Solde</th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalNight = 0;
        $totalDate = [];
        $totalCost = 0;
        $totalBalance = 0;
    @endphp
    @foreach($data as $order)
        @foreach($order as $beneficiary)
            @foreach($beneficiary as $rooms)
                @php
                    $baseData = $rooms->first();
                    $collection = collect($rooms);
                @endphp

                @if (in_array($baseData['accommodation_cart_id'], $roomingList->getAmendedCartsIds()))
                    @continue
                @endif

                <tr>
                    @if ($with_order ?? false)
                        <td class="text-center">
                            <a target="_blank"
                               class="btn btn-sm btn-secondary"
                               href="{{ route('panel.manager.event.orders.edit', ['event' => $event->id, 'order'=>$baseData['order_id']]) }}">{{ $baseData['order_id'] }}</a>
                        </td>
                    @endif
                    <td>{{ $baseData['beneficiary_id']  }} {{ $baseData['participation_type']}}</td>
                    <td>
                        @if ($with_order ?? null)
                            @if($baseData['event_contact_id'])
                                <a href="{{ route('panel.manager.event.event_contact.edit', [
                                                    'event' => $event->id,
                                                    'event_contact' => $baseData['event_contact_id'],
                                                ]) }}">{{ $baseData['last_name'] }}</a>

                            @elseif($baseData['event_group_id'])
                                <a href="{{ route('panel.manager.event.event_group.edit', [
                                                'event' => $event->id,
                                                'event_group' => $baseData['event_group_id'],
                                                 ]) }}">{{ $baseData['last_name'] }}</a>

                            @else
                                {{ $baseData['last_name'] }}
                            @endif
                        @else
                            {{ $baseData['last_name'] }}
                        @endif
                    </td>
                    <td>{{ $baseData['first_name'] }}</td>
                    <td>{{ $baseData['company'] }}</td>
                    <td>{{ $baseData['country'] }}</td>
                    <td>{{ $baseData['invoiceable'] }}</td>
                    <td>{{ $baseData['pec'] ? 'OUI' : 'NON' }}</td>
                    <td>{{ $baseData['email'] }}</td>
                    <td>{{ $baseData['room_category_label'] }}</td>
                    <td>{{ $baseData['room_label'] }}</td>
                    <td>{{ $baseData['roomnotes'] }}</td>
                    <td>{{ $baseData['pax'] }}</td>
                    <td>{!! $baseData['accompanying']  !!}</td>
                    <td>{{ collect($rooms)->min('date') }}</td>
                    <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', collect($rooms)->max('date'))->addDay()->format('d/m/Y') }}</td>
                    @php
                        $cancelled = $collection->filter(fn($item) => !empty($item['cancelled_at']));
                        $quantity = $collection->reject(fn($item) => !empty($item['cancelled_at']))->sum('quantity');
                        $totalNight += $quantity ?? 0;
                    @endphp
                    <td>{{ $quantity }}</td>
                    @foreach($roomingList->bookingsDates() as $date)
                        @php
                            $value = $collection->reject(fn($item) => !empty($item['cancelled_at']))->filter(fn($item) => \Carbon\Carbon::createFromFormat('d/m/Y',$item['date'])->toDateString() == $date)->sum('quantity');
                            $totalDate[$date] ??= 0;
                            $totalDate[$date] += $value ?? 0;
                        @endphp
                        <td>{{ $value }}</td>
                        <td>{{ $baseData['paid_price'] }}</td>
                    @endforeach
                    @php
                        $totalCost += $baseData['accommodation_cost'];
                    @endphp
                    <td>
                        {{ $baseData['accommodation_cost'] - $cancelled->sum('paid_price') }}</td>
                    <td>
                        @php
                            $totalBalance += $baseData['order_status'] == 'paid' ? 0 :  ($baseData['order_total'] - $baseData['payments_total']);
                        @endphp
                        {{ $baseData['order_status'] == 'paid' ? 0 :  ($baseData['order_total'] - $baseData['payments_total']) }}
                    </td>
                </tr>

            @endforeach
        @endforeach
    @endforeach
    </tbody>
    @php
        $colspan = ($with_order ?? false) ? 15 : 14;
    @endphp
    <tfoot>
    <tr>
        <th>Total :</th>
        <th colspan="{{$colspan}}"></th>
        <td>{{$totalNight}}</td>
        @foreach($totalDate as $dateValue)
            <td>{{$dateValue}}</td>
        @endforeach
        <td>{{$totalCost}}</td>
        <td>{{$totalBalance}}</td>
    </tr>
    </tfoot>
</table>
