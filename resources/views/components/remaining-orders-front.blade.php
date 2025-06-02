@php
    use App\Accessors\Dates;

    $remainingPayments = \App\Accessors\OrderAccessor::calculateRemainingAmounts($orders);

@endphp
@foreach($orders as $order)
    <tr data-order-id="{{ $order->id }}" class="align-middle">
        <td>{{ $order->id }}</td>
        <td>{{ $order->created_at->format(Dates::getFrontDateTimeFormat()) }}</td>
        <td>{{ \MetaFramework\Accessors\Prices::readableFormat($order->total_net + $order->total_vat) }}</td>
        <td>{{ \MetaFramework\Accessors\Prices::readableFormat($remainingPayments[$order->id] ?? 0) }}</td>
        <td class="align-middle pt-2">
            <a href="{{route('front.event.orders.edit', [
                            'event' => $order->event_id,
                            'order' => $order->id,
                        ])}}"
               target="_blank"
               class="btn btn-sm btn-primary">{{ __('front/order.detail') }}</a>

            @if($order->isCancelled())
                <span class="d-block d-md-inline-block ps-lg-4 text-danger">{{ __('front/order.cancelled') }}</span>
                >
            @else
                @if($order->hasPendingCancellation())
                    <span
                        class="d-block d-md-inline-block ps-lg-4 text-danger">{{ __('front/order.cancellation_asked') }}</span>
                @else
                    <a href="#"
                       data-bs-toggle="modal"
                       data-bs-target="#remainingPaymentsConfirmModal"
                       class="action-select-remaining-payment btn btn-sm btn-success">{{ __('front/cart.pay') }}</a>
                @endif
            @endif
        </td>
    </tr>
@endforeach
