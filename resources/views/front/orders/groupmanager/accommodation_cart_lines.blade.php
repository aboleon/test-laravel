<tbody>
@php
    use App\Accessors\Dictionnaries;
    use MetaFramework\Accessors\Prices;
    $cartAccommodationRoomCache = [];
    $cartAccommodationCategoryCache = [];
    $cartAccommodationRoomGroupsCache = [];
    $subcart = ['net' => 0, 'vat' => 0];
@endphp
@foreach($suborders as $suborder)

    @foreach($suborder->accommodation as $cart)
        @php
            if (!isset($cartAccommodationRoomCache[$cart->room->room_id])) {
                $cartAccommodationRoomCache[$cart->room->room_id] = Dictionnaries::entry('type_chambres', $cart->room->room_id)->name . ' x ' . $cart->room->capacity;
            }


            $room_label = $cartAccommodationRoomCache[$cart->room->room_id];

            if (!isset($cartAccommodationRoomGroupsCache[$cart->eventHotel->id])) {
                $cartAccommodationRoomGroupsCache[$cart->eventHotel->id] = $cart->eventHotel->roomGroups->pluck('name', 'id')->toArray();
            }


            if (!isset($cartAccommodationCategoryCache[$cart->room->room_group_id])) {
                $cartAccommodationCategoryCache[$cart->room->room_group_id] = $cartAccommodationRoomGroupsCache[$cart->eventHotel->id][$cart->room->room_group_id];
            }

            $room_category = $cartAccommodationCategoryCache[$cart->room->room_group_id];


        @endphp

        <tr data-cart-id="{{ $cart->id }}">
            <td>{{ $suborder->account->names() }}</td>
            <td>

                {{ __('front/accommodation.booked_for', [
                    'number' => $cart->quantity,
                    'people' => trans_choice('ui.person', $cart->quantity),
                    ]),
                }}
                <div class="smaller">
                    {{ trans_choice('ui.hotels.label', 1) }}
                    : {{ strip_tags($hotels[$cart->event_hotel_id],'span') }}
                    <br>
                    {{ $room_label  . ' / '.$room_category}}
                    <br>
                    Date : {{ $cart->date->format('d/m/Y') }}
                    @if($cart->accompanying_details)
                        <br>
                        {{ __('front/accommodation.col_accompany_details') }}
                        : {{ $cart->accompanying_details }}
                    @endif
                    @if($cart->comment)
                        <br>
                        {{ __('front/accommodation.col_comments') }}
                        : {{ $cart->comment }}
                    @endif
                </div>
            </td>
            <td class="text-end">{{ Prices::readableFormat($cart->unit_price) }}</td>
            <td class="text-center">{{ $cart->quantity }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->total_net + $cart->total_vat) }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->total_net) }}</td>
            <td class="text-end">{{ Prices::readableFormat($cart->total_vat) }}</td>
        </tr>
    @endforeach
@endforeach
@if ($suborders->contains(function ($suborder) {
            return $suborder->taxRoom->isNotEmpty();
        }))

    @foreach($suborders as $suborder)
        @foreach($suborder->taxRoom as $cart)
            <tr data-cart-id="{{ $cart->id }}">
                <td>{{ $suborder->account->names() }}</td>
                <td>
                    {{ __('front/accommodation.col_processing_fee') }}<br>
                    <div class="smaller">
                        {{ trans_choice('ui.hotels.label', 1) }}
                        {!! strip_tags($hotels[$cart->event_hotel_id],'span') . '<br>'. $room_label . ' / '.$room_category  !!}
                    </div>
                </td>
                <td class="text-end">{{ Prices::readableFormat($cart->amount) }}</td>
                <td class="text-center">{{ $cart->quantity }}</td>
                <td class="text-end">{{ Prices::readableFormat($cart->amount) }}</td>
                <td class="text-end">{{ Prices::readableFormat($cart->amount_net) }}</td>
                <td class="text-end">{{ Prices::readableFormat($cart->amount_vat) }}</td>

            </tr>
            @php
                $subcart['net'] += $cart->amount_net;
                $subcart['vat'] += $cart->amount_vat;
            @endphp
        @endforeach
    @endforeach
@endif
</tbody>
