@if ($ordered->isEmpty())
    <x-mfw::alert :message="__('front/order.has_no_items_to_attribute')"/>
@else
    <div class="row gx-5 mb-4">
        <div class="col-lg-8 col-12">
            <h5>{{ trans_choice(__('front/order.prestation'),2) }}</h5>
            <table class="table mt-3">
                <thead>
                <tr>
                    <th style="width: 30%">{{ trans_choice('front/order.prestation',1) }}</th>
                    <th style="width: 25%" class="text-end">{{ __('ui.quantity') }}</th>
                    <th style="width:5%" class="text-end">{{ __('front/groups.to_distribute') }}</th>
                    <th style="width:5%" class="text-end">{{ __('front/groups.distributed') }}</th>
                    <th style="width:5%" class="text-end">{{ trans_choice('front/order.remaining',1) }}</th>
                </tr>
                </thead>
                <tbody id="service-cart" data-shoppable="{{ \App\Models\EventManager\Sellable::class }}">

                @foreach($ordered as $service)
                    <x-front-order-service-attribution-row :item="$service"
                                                           :services="$event->sellableService->load('event.services')"
                                                           :event="$event"/>
                @endforeach

                </tbody>
            </table>
            <button class="btn btn-sm btn-success mb-4" type="button" id="service-distributor">
                {{ __('front/order.attribute_to_members') }}
            </button>
            <div id="service-cart-messages" data-ajax="{{ route('ajax') }}"></div>

        </div>

        <div class="col-lg-4 col-12">
            @include('orders.attributions.members')
        </div>

        <div class="col-12">

            <br>
            <br>

            <h5 class="mb-3">{{ __('front/groups.done_attributions') }}</h5>
            <div id="service-attribution-messages" data-ajax="{{ route('ajax') }}"></div>

            @if ($groupMembers->isNotEmpty())

                @php
                    $attributions = $event->sellableService->flatMap(fn($item) => $item->attributions);
                    $event_service = $event->sellableService->pluck('title','id')->toArray();
                @endphp

                <table class="table table-bordered table-sm">
                    @foreach($groupMembers as $member)
                        <tbody class="member-{{ $member->id }}">
                        @php
                            $memberAttributions = $attributions->filter(fn($a) => $a->event_contact_id == $member->id)
                            ->groupBy('shoppable_id')
                            ->map(fn($items) => [
                                'quantity' => $items->sum('quantity'),
                                'affected_date' => $items->last()->created_at->format('d/m/Y'),
                            ]);
                        @endphp
                        <x-front-order-affected-service-row
                            :attributions="$memberAttributions"
                            :services="$event_service"
                            :member="$member"/>
                        <tr class="border-0">
                            <td colspan="4" class="error d-none text-danger"></td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>

            @else
                <x-mfw::alert :message="__('front/groups.has_no_members')"/>
            @endif
        </div>
    </div>

    <template id="affected-service">
        <tr class="text-center affected-service">
            <td class="align-middle text-start service-name" style="width: 40%;"></td>
            <td class="align-middle qty" data-qty data-service-id data-event-contact-id style="width: 20%;"></td>
            <td class="align-middle affected-date" style="width: 20%"></td>
            <td class="align-middle">
                <x-mfw::simple-modal id="delete_attribution_service_row"
                                     class="btn btn-sm btn-primary m-0"
                                     :title="__('front/groups.delete_attribution')"
                                     confirmclass="btn-danger"
                                     :confirm="__('front/groups.delete_attribution_confirm')"
                                     callback="removeAttributionService"
                                     :identifier="Str::random(8)"
                                     :text="__('mfw.delete')"/>
            </td>
        </tr>
    </template>

    @push('callbacks')
        <script>
            function postServiceCreateAttributions(result) {
                if (result.hasOwnProperty('error')) {
                    return;
                }

                let members = $('#members').find(':checked');

                members.each(function () {
                    let memberId = $(this).val();

                    // Loop through each key in the stored result
                    Object.keys(result.stored).forEach(key => {
                        result.stored[key].forEach(item => {
                            if (item.member_id == memberId) {
                                let memberRow = $(`.member-${memberId}.affected-service.service-${key}`);
                                let hasRow = memberRow.length > 0;

                                // If the row does not exist, create it from the template
                                if (!hasRow) {
                                    let baseMemberRow = $(`.base-row.member-${memberId}`);
                                    memberRow = $($('template#affected-service').html());
                                    memberRow.addClass(`member-${memberId} affected-service service-${key}`);
                                    memberRow.find('a').attr('data-identifier', `member-${memberId} affected-service service-${key}`)
                                    baseMemberRow.addClass('d-none');
                                    baseMemberRow.after(memberRow);
                                }

                                // Re-select the row after potentially inserting it
                                memberRow = $(`.member-${memberId}.affected-service.service-${key}`);

                                // Update quantity
                                let totalQty = item.qty;
                                let oldQty = hasRow ? produceNumberFromInput(memberRow.find('.qty').text()) : 0;
                                memberRow.find('.qty')
                                    .addClass('service-' + key)
                                    .text(oldQty + totalQty)
                                    .attr('data-qty', oldQty + totalQty)
                                    .attr('data-service-id', key)
                                    .attr('data-event-contact-id', memberId);

                                let attributionRow = $(`.order-service-attribution-row.service-${key}`);


                                // Append the label to the room cell if it's a new row
                                if (!hasRow) {
                                    let cellLabel = attributionRow.find('label').html();
                                    memberRow.find('.service-name').append(cellLabel);
                                }
                                memberRow.find('.affected-date').text(result.affected_date);
                            }
                        });
                    });
                });
                members.prop('checked', false);
            }

            function postRemoveServiceAttribution(result) {
                console.log('postRemoveServiceAttribution exec');
                if (!result.hasOwnProperty('error')) {
                    let row = $('tr.order-service-attribution-row.service-' + result.input.serviceId),
                        distributed = row.find('.distributed'),
                        remaining = row.find('.remaining'),
                        updatedDistributed = produceNumberFromInput(distributed.text()) - produceNumberFromInput(result.input.qty),
                        updatedRemaining = produceNumberFromInput(remaining.text()) + produceNumberFromInput(result.input.qty);


                    row.find('input.qty').attr('data-distributed', updatedDistributed).attr('data-remaining', updatedRemaining)

                    distributed.text(updatedDistributed);
                    remaining.text(updatedRemaining);

                    $(result.input.identifier).remove();

                    $('.error.member-' + result.input.eventContactId).html('').addClass('d-none');
                    if (!$('.affected-service.member-' + result.input.eventContactId).length) {
                        $('.base-row.member-' + result.input.eventContactId).removeClass('d-none');
                    }
                }
            }

            function removeAttributionService() {

                $('.delete_attribution_service_row').click(function () {

                    let identifier = '.' + $(this).attr('data-identifier').replace(/\s+/g, '.'),
                        dataAttributes = $.param($(identifier).find('td.qty').data());

                    $.when(
                        ajax('action=removeFrontServiceAttribution&callback=postRemoveServiceAttribution&identifier=' + identifier + '&' + dataAttributes, $('#service-attribution-messages'))).then(
                        $('#mfw-simple-modal').find('.btn-cancel').trigger('click')
                    );

                });
            }
        </script>
    @endpush
    @push('js')
        <script>
            const myServiceCart = new AttributionCart('service');
            myServiceCart.init();
        </script>
    @endpush

@endif
