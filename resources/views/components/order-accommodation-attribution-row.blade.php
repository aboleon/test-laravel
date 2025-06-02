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
        .form-check {
            display: flex;
        }
    </style>
@endpushonce

<tr class="order-accommodation-attribution-row accommodation-{{ $cart->id }}" data-identifier="{{ $identifier }}">
    <td class="item">
        <x-mfw::checkbox name="shopping_cart_accommodation[id][]"
                         value="{{ $cart->id }}"
                         label="{{ $room->room->name  .' x '.$room->capacity .' / '. $room->group->name }}"/>
        <div>
            {!! $hotels[$cart->event_hotel_id] !!}
        </div>
        <small class="d-block text-danger d-none error"></small>
    </td>
    <td class="service-date">
        {{ $cart->date->format('d/m/Y') }}
    </td>
    <td class="qty">
        @php
            $params = [
                'data-room-id' => $cart->room_id,
                'data-bought' => $qty,
                'data-distributed' => $distributed_qty,
                'data-remaining' => $qty - $distributed_qty
            ];
        @endphp
        <x-mfw::number name="shopping_cart_accommodation[qty][]" class="qty" value="1" min="1"
                       :params="$params"/>
    </td>
    <td class="bought">
        {{ $qty }}
    </td>
    <td class="distributed">
        {{ $distributed_qty }}
    </td>
    <td class="remaining">{{ $qty - $distributed_qty }}</td>
</tr>
