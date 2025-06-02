@if ($ordered->isEmpty())
    <x-mfw::alert :message="__('front/order.has_no_items_to_attribute')"/>
@else
    @php
        $hotels = \App\Accessors\EventManager\Accommodations::hotelLabelsWithStatus($event);
        $cartType = \App\Enum\OrderCartType::ACCOMMODATION->value;
        $rooms = $ordered->flatten()
        ->map(fn($item) => [
             'capacity' => $item->capacity,
             'room_group_id' => $item->room_group_id,
             'room_id' => $item->room_id,
             'room_category' => json_decode($item->room_category)->{$locale},
             'room'=> json_decode($item->room)->{$locale}
             ])
             ->unique();

         $attributions = $event->accommodationAttributions;


    @endphp
    <div class="row gx-5 mb-4">
        <div class="col-lg-8 col-12" id="{{ $cartType }}-cart">
            <h4>{{ __('front/accommodation.accommodation') }}</h4>
            @foreach($ordered as $item)
                @php
                    $hotel = $item->first()->first();
                @endphp
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <h5 class="fs-6 fw-bold py-2 px-3 badge rounded-pill text-bg-warning">{{ $hotel->hotel_name }}</h5>
                            @if($hotel->stars)
                                <ul class="list-inline ms-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <li class="list-inline-item me-0 small">
                                            @if ($i <= $hotel->stars)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        </li>
                                    @endfor
                                </ul>
                            @endif
                        </div>

                        <div class="fs-12">
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ $hotel->hotel_address }}
                        </div>
                    </div>

                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-dark">{{ __('mfw.date') }}</th>
                        <th class="text-dark">{{ __('forms.fields.type') }}</th>
                        <th class="text-end text-dark d-none">{{ __('ui.quantity') }}</th>
                        <th class="text-end text-dark">{{ __('front/order.ordered') }}</th>
                        <th class="text-end text-dark">Attributions</th>
                        <th class="text-end text-dark">{{ trans_choice('front/order.remaining',2) }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @php
                        $control = \App\Accessors\AttributionAccessor::accommodationSummary($item);

                    @endphp
                    @foreach($item as $date => $entries)


                        <tr>
                            <td rowspan="{{ $entries->count()+1 }}">
                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}
                            </td>
                        </tr>
                        @foreach($entries as $item)
                            @php
                                $attributed = $attributions->filter(fn($a) => $a->shoppable_id == $item->room_id && $a->attributes['date'] == $date)->sum('quantity');
                            @endphp
                            <tr class="order-{{ $cartType }}-attribution-row {{ $item->room_id.'-'.$date }}"
                                data-date="{{ $date }}">
                                <td>
                                    <x-mfw::checkbox name="shopping_cart_{{ $cartType }}[id][]"
                                                     value="{{ $item->room_id }}"
                                                     :params="!$control[$date]['can_attribute'] ? ['disabled' => 'disabled'] : []"
                                                     label="{{ json_decode($item->room)->{$locale} }} x {{ $item->capacity }}p.
                                    <br><small>{!! json_decode($item->room_category)->{$locale} !!}</small>"/>
                                    @if(!$control[$date]['can_attribute'])
                                        <small
                                            class="text-danger">{{ __('front/order.attributions_not_authorized') }}</small>
                                    @endif
                                </td>
                                <td class="qty d-none">
                                    <x-mfw::number name="shopping_cart_{{ $cartType }}[qty][]" class="qty"
                                                   value="1"
                                                   min="1"
                                                   :readonly="true"
                                                   :params="[
                'data-room-id' => $item->room_id,
                'data-bought' => $item->total_quantity,
                'data-distributed' => $attributed,
                'data-remaining' => $item->total_quantity - $attributed
            ]"/>
                                </td>
                                <td class="text-end bought">{{ $item->total_quantity }}</td>
                                <td class="text-end distributed">{{ $attributed }}</td>
                                <td class="text-end remaining">{{ $item->total_quantity - $attributed }}</td>
                            </tr>
                    @endforeach
                    @endforeach
                </table>
            @endforeach
            <button class="btn btn-sm btn-success mb-4" type="button" id="{{ $cartType }}-distributor">
                {{ __('front/order.attribute_to_members') }}
            </button>
            <div id="{{ $cartType }}-cart-messages" data-ajax="{{ route('ajax') }}"></div>
        </div>

        <div class="col-lg-4 col-12">
            @include('orders.attributions.members')
        </div>

        <div class="col-12">

            <br><br>
            <h5 class="mb-3">{{ __('front/groups.done_attributions') }}</h5>
            <div id="accommodation-attribution-messages" data-ajax="{{ route('ajax') }}"></div>

            @if ($groupMembers->isNotEmpty())

                <table class="table table-bordered table-sm">
                    <tbody>
                    @foreach($groupMembers as $member)
                        <tr class="member member-{{ $member->id }}">
                            <th colspan="2" class="border-0 pt-3 pb-2 text-dark">
                                {{ $member->name }}
                            </th>
                            <th colspan="2" class="border-0 align-middle error text-danger"></th>
                        </tr>
                        @php
                            $bookingsByMember = $bookedForMembers->filter(fn($item) => $item->client_id == $member->user_id);
                            $excludedIds = $attributions->filter(fn($item) => $item->event_contact_id == $member->id)->pluck('id')->all();

                            $crossAttributionsByMember = $attributionsForMembers
                            ->filter(fn($item) => $item->event_contact_id == $member->id)
                            ->reject(fn($item) => in_array($item->attribution_id, $excludedIds));

                                $memberAttributions = $attributions
                                ->filter(fn($item) => $item->event_contact_id == $member->id)
                                ->groupBy([
                                    fn($item) => $item->attributes['date'],
                                    'shoppable_id'
                                ])
                                ->map(function ($datesGroup) {
                                    return $datesGroup->map(function ($items) {
                                        $totalQuantity = $items->sum('quantity');
                                        $lastCreatedAt = $items->last()->created_at->format('d/m/Y');

                                        return [
                                        'id' => $items->first()->id,
                                        'order_id' => $items->first()->order_id,
                                            'quantity' => $totalQuantity,
                                            'affected_date' => $lastCreatedAt
                                        ];
                                    });
                                });
                        @endphp
                        <tr>
                            <td colspan="3" class="border-0 border-end-1 bookings member-{{ $member->id }}">
                                @if($bookingsByMember->isNotEmpty())
                                    <small class="text-danger fw-bold mb-2 d-block">
                                    {{ __('front/groups.member_has_bookings') }}
                                    </small>
                                    @foreach($bookingsByMember as $booking)
                                        <small data-date="{{ $booking->date->format('Y-m-d') }}" style="font-size: 13px"
                                               class="d-block text-dark">{{ $booking->date->format('d/m/Y') }}
                                            , {{ $booking->hotel_name .  ', ' . $booking->room_label .' x ' . $booking->room_capacity .'p. / '. $booking->room_category }}
                                            ,
                                            {{ strtolower(trans_choice('front/order.order', 1)) }} #{{ $booking->order_id }}</small>
                                    @endforeach
                                @endif
                            </td>
                            <td colspan="3" class="border-0 cross-attributions member-{{ $member->id }}">
                                @if($crossAttributionsByMember->isNotEmpty())
                                    <small class="text-danger fw-bold mb-2 d-block">
                                        {{ __('front/groups.member_has_attributions') }}
                                        </small>
                                    @foreach($crossAttributionsByMember as $booking)
                                        <small data-attrubition-uuid="{{ $booking->attribution_id . '-' .$booking->order_id }}"
                                               data-date="{{ $booking->date }}" style="font-size: 13px"
                                               class="d-block text-dark">{{ $booking->date_formatted }}
                                            , {{ $booking->hotel_name .  ', ' . $booking->room_label .' x ' . $booking->room_capacity .'p. / '. $booking->room_category }}
                                            ,
                                            {{ strtolower(trans_choice('front/order.order', 1)) }} #{{ $booking->order_id }}</small>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ trans_choice('front/order.order', 1) }}</th>
                            <th>{{ __('ui.date') }}</th>
                            <th>{{ trans_choice('front/accommodation.room_',1) }}</th>
                            <th>{{ __('ui.quantity') }}</th>
                            <th>{{ __('front/groups.attributed_on') }}</th>
                            <th class="text-end">Actions</th>
                        </tr>

                        <tr class="{{ $memberAttributions->isNotEmpty() ? 'd-none ' : '' }}base-row member-{{ $member->id }}">
                            <td colspan="6">
                                <x-mfw::alert type="warning"
                                              class="py-1 px-2 mx-0 my-1"
                                              :message="__('front/groups.member_has_no_attributions')"/>
                            </td>
                        </tr>
                        @if($memberAttributions->isNotEmpty())
                            @foreach($memberAttributions as $date => $attribution)

                                @php
                                    $formattedDate =  \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
                                @endphp

                                @foreach($attribution as $room_id => $values)

                                    @php
                                        $identifier = 'affected-service member-'.$member->id. ' '. \App\Enum\OrderCartType::ACCOMMODATION->value. '-'.$room_id.'-'.$date;
                                        $room = $rooms->where('room_id',$room_id)->first();
                                    @endphp

                                    <tr class="text-center {{ $identifier }}"
                                        data-attrubition-uuid="{{ $values['id'] . '-' .$values['order_id'] }}">
                                        <td class="align-middle order_id">#{{ $values['order_id'] }}</td>
                                        <td class="align-middle date">{{ $formattedDate }}</td>
                                        <td class="align-middle text-start room"
                                            style="width: 40%;">
                                            @if ($room)
                                                {{ $room['room'] }} x {{ $room['capacity'] }}p.
                                                / {{ $room['room_category'] }}

                                            @else
                                                NA
                                            @endif
                                        </td>
                                        <td class="align-middle qty {{ $room_id .'-'.$date }}"
                                            data-qty="{{ $values['quantity'] }}"
                                            data-room-id="{{ $room_id }}"
                                            data-date="{{ $date }}"
                                            data-event-contact-id="{{ $member->id }}"
                                            style="width: 20%;">{{ $values['quantity'] }}</td>
                                        <td class="align-middle affected-date" style="width: 20%">
                                            {{ $values['affected_date'] }}
                                        </td>
                                        <td class="align-middle text-end">
                                            <x-mfw::simple-modal id="delete_attribution_accommodation_row"
                                                                 class="btn btn-sm btn-primary m-0"
                                                                 :title="__('front/groups.delete_attribution')"
                                                                 confirmclass="btn-danger"
                                                                 :confirm="__('front/groups.delete_attribution_confirm')"
                                                                 callback="removeAttributionAccommodation"
                                                                 :identifier="$identifier"
                                                                 :text="__('mfw.delete')"/>
                                        </td>
                                    </tr>
                    @endforeach
                    @endforeach
                    @endif

                    @endforeach

                </table>

            @else
                <x-mfw::alert :message="__('front/groups.has_no_members')"/>
            @endif

        </div>
    </div>

    <template id="affected-accommodation">
        <tr class="text-center affected-service">
            <td class="align-middle order_id"></td>
            <td class="align-middle date"></td>
            <td class="align-middle text-start room" style="width: 40%;">
            </td>
            <td class="align-middle qty" data-qty data-room-id data-date style="width: 20%;"></td>
            <td class="align-middle affected-date" style="width: 20%">
            </td>
            <td class="align-middle text-end">
                <x-mfw::simple-modal id="delete_attribution_accommodation_row"
                                     class="btn btn-sm btn-primary m-0"
                                     :title="__('front/groups.delete_attribution')"
                                     confirmclass="btn-danger"
                                     :confirm="__('front/groups.delete_attribution_confirm')"
                                     callback="removeAttributionAccommodation"
                                     :identifier="Str::random(8)"
                                     :text="__('mfw.delete')"/>

            </td>
        </tr>
    </template>



    @push('callbacks')
        <script>

            function postCreateAccommodationAttributions(result) {

                console.log('postCreateAccommodationAttributions FFF');

                let members = $('#members').find(':checked');

                members.each(function () {
                    let memberId = $(this).val();

                    Object.keys(result.stored).forEach(key => {
                        result.stored[key]
                            .sort((a, b) => new Date(a.date) - new Date(b.date)) // Sort items by date
                            .forEach(item => {

                                if (item.member_id == memberId) {
                                    let memberRow = $(`.member-${memberId}.affected-service.accommodation-${key}-${item.date}`);
                                    let hasRow = memberRow.length > 0;

                                    if (!hasRow) {
                                        let baseMemberRow = $(`.base-row.member-${memberId}`);
                                        memberRow = $($('template#affected-accommodation').html());
                                        memberRow.addClass(`member-${memberId} affected-service accommodation-${key}-${item.date}`);
                                        memberRow.find('a').attr('data-identifier', `affected-service member-${memberId} accommodation-${key}-${item.date}`)
                                        baseMemberRow.addClass('d-none');
                                        // Find the next row based on date order
                                        let nextRow = baseMemberRow.nextAll(`.affected-service`).filter(function () {
                                            let rowDate = $(this).find('.date').text().split('/').reverse().join('-');
                                            return new Date(rowDate) > new Date(item.date);
                                        }).first();

                                        if (nextRow.length) {
                                            memberRow.insertBefore(nextRow);
                                        } else {
                                            memberRow.insertAfter(baseMemberRow);
                                        }
                                    }

                                    memberRow = $(`.member-${memberId}.affected-service.accommodation-${key}-${item.date}`);

                                    let totalQty = item.qty;
                                    let firstItemDate = item.date;
                                    let oldQty = hasRow ? produceNumberFromInput(memberRow.find('.qty.' + key + '-' + firstItemDate).text()) : 0;

                                    if (!hasRow) {
                                        memberRow.find('.qty').addClass(key + '-' + firstItemDate);
                                    }
                                    memberRow.find('.qty').text(oldQty + totalQty).attr('data-qty', oldQty + totalQty).attr('data-room-id', key).attr('data-date', item.date).attr('data-event-contact-id', item.member_id);

                                    let attributionRow = $(`.order-accommodation-attribution-row.${key}-${firstItemDate}`);
                                    let distributed = attributionRow.find('.distributed');
                                    let remaining = attributionRow.find('.remaining');

                                    distributed.text(produceNumberFromInput(distributed.text()) + totalQty);
                                    remaining.text(produceNumberFromInput(remaining.text()) - totalQty);

                                    if (!hasRow) {
                                        let roomLabelHtml = attributionRow.find('label').html();
                                        memberRow.find('.room').append(roomLabelHtml);
                                        memberRow.find('.date').text(item.date_formated);
                                        memberRow.find('.order_id').text('#' + item.order_id);
                                    }

                                    memberRow.find('.affected-date').text(result.affected_date);
                                }
                            });
                    });
                });
                members.prop('checked', false);
            }


            function postRemoveAttributionAccommodation(result) {
                if (!result.hasOwnProperty('error')) {
                    let row = $('tr.order-accommodation-attribution-row.' + result.input.roomId + '-' + result.input.date),
                        distributed = row.find('.distributed'),
                        remaining = row.find('.remaining'),
                        updatedDistributed = produceNumberFromInput(distributed.text()) - produceNumberFromInput(result.input.qty),
                        updatedRemaining = produceNumberFromInput(remaining.text()) + produceNumberFromInput(result.input.qty);

                    row.find('input.qty').attr('data-distributed', updatedDistributed).attr('data-remaining', updatedRemaining)

                    distributed.text(updatedDistributed);
                    remaining.text(updatedRemaining);
                    $('tr' + result.input.identifier).remove();

                    if (!$('.affected-service.member-' + result.input.eventContactId).length) {
                        $('.base-row.member-' + result.input.eventContactId).removeClass('d-none');
                    }
                }
            }

            function removeAttributionAccommodation() {

                $('.delete_attribution_accommodation_row').off().click(function () {
                    let identifier = '.' + $(this).attr('data-identifier').replace(/\s+/g, '.'),
                        dataAttributes = $.param($(identifier).find('td.qty').data());
                    $.when(
                        ajax('action=removeFrontAccommodationAttribution&callback=postRemoveAttributionAccommodation&identifier=' + identifier + '&' + dataAttributes, $('#accommodation-attribution-messages'))).then(
                        $('#mfw-simple-modal').find('.btn-cancel').trigger('click')
                    );

                });
            }
        </script>
    @endpush
    @push('js')
        <script>
            const myServiceCart = new AttributionCart('accommodation');
            myServiceCart.init();

        </script>
    @endpush

@endif
