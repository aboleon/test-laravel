<tbody>
@php
    use MetaFramework\Accessors\Prices;
    $cartSellableCache = [];
    $cartSellableGroupCache = [];
@endphp
@foreach($suborders as $suborder)
    @foreach($suborder->services as $cart)
        @php
            if (!isset($cartSellableCache[$cart->service_id])) {
                $cartSellableCache[$cart->service_id] = $services->where('id', $cart->service_id)->first();
            }
            $cartSellable = $cartSellableCache[$cart->service_id];

            if ($cartSellable && !isset($cartSellableGroupCache[$cartSellable->service_group])) {
                $cartSellableGroupCache[$cartSellable->service_group] = $cartSellable->event->services->where('id', $cartSellable->service_group)->first();
            }
            $cartSellableGroup = $cartSellableGroupCache[$cartSellable->service_group] ?? null;

        @endphp

        <tr data-cart-id="{{ $cart->id }}">
            <td>{{ $suborder->account->names() }}</td>
            <td>{{  $cartSellable?->title ?? 'NC' }} - {{ $cartSellableGroup?->name }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->unit_price) }}</td>
            <td class="text-center">{{ $cart->quantity }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->total_net + $cart->total_vat) }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->total_net) }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->total_vat) }}</td>
        </tr>
    @endforeach
@endforeach
</tbody>
