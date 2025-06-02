<div id="order-details" data-event-id="{{ $event->id }}" data-order-id="{{ $order->id }}">
    {{-- TODO: le nom du groupe ne semble pas correctement enregistré dans Invoiceable --}}
    @include('front.shared.invoiceable')

    {{-- Recap pour les paniers achetés en front depuis un group manager --}}
    @php
        use MetaFramework\Accessors\Prices;
        
            $suborders = $order->suborders->load('services','accommodation','taxRoom','account');
            $hasServices = $suborders->contains(function ($suborder) {
                return $suborder->services->isNotEmpty();
            });
            $hasBookings = $suborders->contains(function ($suborder) {
                return $suborder->accommodation->isNotEmpty();
            });
    @endphp

    @if ($hasServices)
        @php
            $services = $event->sellableService->load('event.services');
        @endphp
    @endif

    @if ($hasBookings)
        @php
            $hotels = \App\Accessors\EventManager\Accommodations::hotelLabelsWithStatus($event);
        @endphp
    @endif

    @if ($hasServices or $hasBookings )
        @php
            $totals = $orderAccessor->getOrderTotals();
        @endphp
        <table class="table table-sm table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Participant</th>
                <th>{{ __('front/order.service') }}</th>
                <th>{{ __('front/order.unit_price') }}</th>
                <th>{{ __('ui.quantity') }}</th>
                <th>{{ __('front/order.total_amount') }}</th>
                <th>{{ __('front/order.total_net') }}</th>
                <th>{{ __('front/order.total_vat') }}</th>
            </tr>
            </thead>

            @if ($hasServices)
                @include('front.orders.groupmanager.service_cart_lines')
            @endif

            @if ($hasBookings)
                @include('front.orders.groupmanager.accommodation_cart_lines')
            @endif

            <tfoot>
            <tr>
                <th colspan="4"></th>
                <th class="text-end">{{ Prices::readableFormat($totals['net'] + $totals['vat'])  }}</th>
                <th class="text-end">{{ Prices::readableFormat($totals['net']) }}</th>
                <th class="text-end">{{ Prices::readableFormat($totals['vat']) }}</th>
            </tr>
            </tfoot>
        </table>
    @endif

</div>
