@php
    use MetaFramework\Accessors\Prices;
@endphp
@foreach($grantDepositCarts as $gdCart)
    <tr class="align-middle" data-id="{{ $gdCart->id }}" data-type="grant-deposit">
        <td>
            {{ __('front/order.grant_deposit') }}
            @if($eventTimeline['days_to_event'] < 30)
                <br><b>{{ $gdCart->grant->title }}</b>
            @endif
        </td>
        <td>{{ Prices::readableFormat($gdCart->unit_price) }}</td>
        <td>{{ $gdCart->quantity }}</td>

        @if($this->orderAccessor->isInvoiced())
            <td>{{ Prices::readableFormat($gdCart->total_net + $gdCart->total_vat) }}</td>
            <td>{{ Prices::readableFormat($gdCart->total_net) }}</td>
            <td>{{ Prices::readableFormat($gdCart->total_vat) }}</td>
        @else
            <td></td>
            <td></td>
            <td></td>
        @endif
        <td>{{ __('ui.no') }}</td>
        <td></td>
    </tr>
@endforeach
