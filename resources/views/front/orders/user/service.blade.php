@php
    use MetaFramework\Accessors\Prices;
@endphp
@foreach($serviceCarts as $serviceCart)
    <tr class="align-middle" data-id="{{ $serviceCart->id }}" data-type="service">
        <td>{{ $serviceCart->service->title }}</td>
        <td class="text-end">{{ Prices::readableFormat($serviceCart->unit_price) }}</td>
        <td class="text-center">{{ $serviceCart->quantity }}</td>
        <td class="text-end">{{ Prices::readableFormat($serviceCart->total_net + $serviceCart->total_vat)}}</td>
        <td class="text-end">{{ Prices::readableFormat($serviceCart->total_net) }}</td>
        <td class="text-end">{{ Prices::readableFormat($serviceCart->total_vat) }}</td>
        <td class="text-end">{{ Prices::readableFormat($serviceCart->total_pec) }}</td>
        <td>
            @if($serviceCart->cancelled_at)
                <div class="alert alert-danger mt-3">
                    {{ __('front/order.cancelled') }}
                </div>
            @elseif($serviceCart->cancellation_request)
                <div class="alert alert-danger mt-3">
                    {{ __('front/order.cancellation_asked') }}
                </div>
            @else
                @if($this->eventAccessor->hasNotStarted() && $this->orderAccessor->isNotGroup())
                    <button class="btn btn-sm btn-primary btn-cancel-cart-line">
                        {{ __('front/order.ask_for_cancellation') }}
                    </button>
                @endif
            @endif
        </td>
    </tr>
@endforeach
