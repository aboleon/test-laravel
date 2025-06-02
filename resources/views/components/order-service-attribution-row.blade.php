@pushonce('css')
    <style>
        .order-service-attribution-row .mfw-edit-link {
            width: 25px;
            height: 25px;
            padding: 0;
        }

        .order-service-attribution-row .mfw-edit-link i {
            font-size: 12px;
        }
    </style>
@endpushonce

<tr class="order-service-attribution-row service-{{ $cart->service_id }}" data-identifier="{{ $identifier }}">
    <td class="item text-nowrap">
        <x-mfw::checkbox name="shopping_cart_service[id][]"
                         value="{{ $cart->service_id }}"
                         :label="($sellable?->title ?? 'NC')"/>
        <small class="d-block max-qty" data-max="{{ $group->max }}">
            Nb Max de résas : {{ $group->max }}
        </small>
        <small class="d-block text-danger d-none error"></small>
    </td>
    <td>{{ $group->name }}</td>
    <td class="service-date">{{ $cart->service->service_date }}</td>
    <td class="qty">
        @php
            $params = [
                'data-service-id' => $cart->service_id,
                'data-max'=> (int)($group->max) ?: 1,
                'data-bought' => $qty,
                'data-distributed' => $distributed_qty,
                'data-remaining' => $qty - $distributed_qty
            ];
        @endphp
        <x-mfw::number name="shopping_cart_service[qty][]" class="qty" value="1" min="1"
                       :params="$params"/>
    </td>
    <td class="bought text-end">
        @foreach($grouped as $subcart)
            {{ $subcart->quantity }}<br/>
        @endforeach
        @if ($multiple)
            <div class="border-top border-1 border-dark-subtle">
                <b>{{ $qty }}</b>
            </div>
        @endif
    </td>
    <td class="distributed text-end">
        {{ $distributed_qty }}
    </td>
    <td class="remaining text-end">{{ $qty - $distributed_qty }}</td>
    <td>
        <a href="{{ route('panel.manager.event.sellable.edit', ['event'=>$event->id, 'sellable' => $cart->service_id]) }}"
           target="_blank" class="fs-6 float-end mfw-edit-link btn btn-sm btn-secondary" data-bs-toggle="tooltip"
           data-bs-placement="top" data-bs-title="Éditer">
            <i class="fas fa-pen"></i></a>
    </td>
</tr>
