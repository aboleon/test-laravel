@php
use MetaFramework\Accessors\Prices;
@endphp
@foreach($sellableDepositCarts as $sdCart)
    <tr class="align-middle" data-id="{{ $sdCart->id }}" data-type="sellable-deposit">
        <td>{{ __('front/order.deposit') }} - {{ $sdCart->sellable->title }}</td>
        <td>{{ Prices::readableFormat($sdCart->total_net + $sdCart->total_vat) }}</td>
        <td>{{ $sdCart->quantity }}</td>
        @if($this->orderAccessor->isInvoiced())
            <td>{{ Prices::readableFormat($sdCart->total_net + $sdCart->total_vat) }}</td>
            <td>{{ Prices::readableFormat($sdCart->total_net) }}</td>
            <td>{{ Prices::readableFormat($sdCart->total_vat) }}</td>
        @else
            <td></td>
            <td></td>
            <td></td>
        @endif
        <td>{{ __('ui.no') }}</td>
        <td></td>
    </tr>
@endforeach
