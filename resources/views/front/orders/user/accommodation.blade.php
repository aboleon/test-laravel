@php
    use App\Accessors\Front\Sellable\Accommodation;use App\Helpers\DateHelper;use Carbon\Carbon;use MetaFramework\Accessors\Prices;
@endphp
@foreach($accommodationCarts as $accommodationCart)
    @php
        $dateFormatted = DateHelper::getFrontDate(Carbon::create($accommodationCart->date));
        $pax = $accommodationCart->quantity + $order->accompanying->filter(fn($item) => $item->room_id == $accommodationCart->room_id)->sum('total');
        $comment_line = Accommodation::getCommentsText($accommodationCart);
        $accommodation_line = Accommodation::getAccompanyingText($accommodationCart);
    @endphp
    <tr class="align-middle"
        data-id="{{ $accommodationCart->id }}"
        data-type="accommodation">
        <td>
            {{ $accommodationCart->roomGroup->name }} - {{ $accommodationCart->room->room->name }}
            <br>
            <div class="smaller">
                {{ trans_choice('ui.hotels.label', 1) }}
                : {{ $accommodationCart->eventHotel->hotel->name }}
                <br>
                Date : {{ $dateFormatted }}
                @if($accommodation_line)
                    <br>
                    {{ __('front/accommodation.col_accompany_details') }}
                    : {!! $accommodation_line !!}
                @endif
                @if($comment_line)
                    <br>
                    {{ __('front/accommodation.col_comments') }} : {!! $comment_line !!}
                @endif
                @if ($order->amended_order_id)
                    <br>
                    <a href="{{ route('front.event.orders.edit', [
                'locale' => app()->getLocale(),
                'event' => $order->event_id,
                'order' => $order->amended_order_id,
             ]) }}"
                       class="text-danger">{{ __('front/order.amended_order', ['order' => $order->amended_order_id]) }}</a>
                @endif

                @php
                    $wasAmended = ($accommodationCart->wasAmended or !is_null($order->amended_by_order_id));
                @endphp
                @if ($wasAmended)
                    @php
                        $this->disableOrderAlteration();
                        $amendedBy = $order->amended_by_order_id ?: $accommodationCart->wasAmended?->order_id;
                    @endphp
                    <br>
                    <a href="{{ route('front.event.orders.edit', [
                'locale' => app()->getLocale(),
                'event' => $order->event_id,
                'order' => $amendedBy,
             ]) }}"
                       class="text-danger">{{ __('front/order.was_amended', ['order' => $amendedBy]) }}</a>
                @endif
            </div>
        </td>
        <td class="text-end">{{ Prices::readableFormat($accommodationCart->unit_price) }}</td>
        <td class="text-center">{{ $accommodationCart->quantity }}</td>
        <td class="text-end">
            @if($accommodationCart->amended_cart_id)
                <del>{{ Prices::readableFormat($accommodationCart->total_net + $accommodationCart->total_vat) }}</del>
            @else
                {{ Prices::readableFormat($accommodationCart->total_net + $accommodationCart->total_vat) }}
            @endif
        </td>
        <td class="text-end">
            @if($accommodationCart->amended_cart_id)
                <del>{{ Prices::readableFormat($accommodationCart->total_net) }}</del>
            @else
                {{ Prices::readableFormat($accommodationCart->total_net) }}
            @endif
        </td>
        <td class="text-end">
            @if($accommodationCart->amended_cart_id)
                <del>{{ Prices::readableFormat($accommodationCart->total_vat) }}</del>
            @else
                {{ Prices::readableFormat($accommodationCart->total_vat) }}
            @endif

        </td>
        <td class="text-end">{{ Prices::readableFormat($accommodationCart->total_pec) }}</td>
        <td>

            @if($accommodationCart->cancelled_at)
                <div class="alert alert-danger mt-3">
                    {{ __('front/order.cancelled') }}
                </div>
            @elseif($accommodationCart->cancellation_request)
                <div class="alert alert-danger mt-3">
                    {{ __('front/order.cancellation_asked') }}
                </div>
            @else
                @if($this->eventAccessor->hasNotStarted() && !$wasAmended)
                    <button class="btn btn-sm btn-primary btn-cancel-cart-line">
                        {{ __('front/order.ask_for_cancellation') }}
                    </button>

                    @if (($this->orderAccessor->isSuborder() or $this->orderAccessor->isInvoiced()) && is_null($order->amended_order_id))
                        <br>
                        <a href="{{ route('front.event.amend.accommodation.cart', [
                                                'locale' =>app()->getLocale(),
                                                'event' => $order->event_id,
                                                'cart' => $accommodationCart->id,
                                              ]) }}" class="btn btn-sm btn-primary btn-amend-booking">
                            {{ __('front/order.amend') }}
                        </a>
                    @endif

                @endif
            @endif


        </td>
    </tr>
@endforeach

@if ($order->amended_order_id)
    @php
        $accommodationCartTotals = $this->orderAccessor->accommodationCartTotals();
    @endphp
    <tr>
        <th colspan="3" class="text-end">Total</th>
        <th colspan="5"
            class="text-decoration-line-through">{{ Prices::readableFormat($accommodationCartTotals['total_net'] + $accommodationCartTotals['total_vat']) }}</th>
    </tr>
    <tr>
        <th colspan="3" class="text-end">{{ __('front/order.supplement_paid') }}</th>
        <th colspan="5">{{ Prices::readableFormat($order->total_net + $order->total_vat) }}</th>
    </tr>
@endif

@foreach($taxRoomCarts as $taxRoomCart)
    <tr class="align-middle" data-id="{{ $taxRoomCart->id }}" data-type="service">
        <td>
            {{ __('front/accommodation.col_processing_fee') }} {{ __('front/accommodation.accommodation') }}
            <div class="smaller">
                {{ trans_choice('ui.hotels.label', 1) }}
                : {{ $accommodationCart->eventHotel->hotel->name }}
                <br>
                {{ $accommodationCart->room->group->name }}
                - {{ $accommodationCart->room->room->name }}
                <br>
                Date : {{ $dateFormatted }}
            </div>
        </td>
        <td class="text-end">{{ Prices::readableFormat($taxRoomCart->amount) }}</td>
        <td class="text-center">{{ $taxRoomCart->quantity }}</td>
        <td class="text-end">{{ Prices::readableFormat($taxRoomCart->amount_net + $taxRoomCart->amount_vat) }}</td>
        <td class="text-end">{{ Prices::readableFormat($taxRoomCart->amount_net) }}</td>
        <td class="text-end">{{ Prices::readableFormat($taxRoomCart->amount_vat) }}</td>
        <td class="text-end">{{ Prices::readableFormat($taxRoomCart->amount_pec) }}</td>
        <td></td>


    </tr>
@endforeach
