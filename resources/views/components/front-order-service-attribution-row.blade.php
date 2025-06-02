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
<tr class="order-service-attribution-row service-{{ $item->service_id }}" data-identifier="{{ $identifier }}">
    <td class="item">
        <x-mfw::checkbox name="shopping_cart_service[id][]"
                         value="{{ $item->service_id }}"
                         :params="!$can_attribute ? ['disabled' => 'disabled'] : []"
                         :label="json_decode($item->service_name)->{$locale}"/>
        <small class="d-block max-qty" data-max="{{ $group->max }}">
            Max par membre : {{ $group->max }}
        </small>
        @if (!$can_attribute)
            <small class="d-block text-danger">Les attributions ne sont pas autorisées ici</small>
        @endif
        <small class="d-block text-danger d-none error"></small>
    </td>
    <td class="qty">
        @php
            $params = [
                'data-service-id' => $item->service_id,
                'data-max'=> (int)($group->max) ?: 1,
                'data-bought' => $item->ordered,
                'data-distributed' => $item->attributed,
                'data-remaining' => $item->ordered - $item->attributed
            ];
        @endphp
        <x-mfw::number name="shopping_cart_service[qty][]"
                       :readonly="!$can_attribute"
                       class="qty text-end" value="1" min="1"
                       :params="$params"/>
    </td>
    <td class="bought text-end align-top">
        {{ $item->ordered }}
        <small class="d-block">Total :{{ $item->originally_ordered }}</small>
    </td>
    <td class="distributed text-end align-top">
        {{ $item->attributed }}
    </td>
    <td class="remaining text-end align-top">{{ $item->ordered - $item->attributed }}</td>
</tr>
