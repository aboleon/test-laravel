@php

    use App\Accessors\Order\Orders;
    use App\Helpers\DateHelper;
    use Carbon\Carbon;
    use MetaFramework\Accessors\Prices;

    $invoiceable = $order->invoiceable;
    $serviceCarts = $this->orderAccessor->serviceCart();
    $accommodationCarts = $this->orderAccessor->accommodationCart();
    $sellableDepositCarts = $this->orderAccessor->sellableDepositCart();
    $grantDepositCarts = $this->orderAccessor->grantDepositCart();
    $taxRoomCarts = $this->orderAccessor->taxRoomCart();
    $hasAnyPayments = $order->hasAnyPayments();
    $eventAccessor = (new \App\Accessors\EventAccessor($event));
    $eventTimeline = $eventAccessor->timeline();


    if ($this->orderAccessor->isSuborder()) {
        $invoiceable = $order->parentOrder->invoiceable;
    }
@endphp


<div id="order-details" data-event-id="{{ $order->event_id }}" data-order-id="{{ $order->id }}">
    @include('front.shared.invoiceable')

    @if(!$this->orderAccessor->isSamePayer())
        <h5>{{ __('ui.beneficiary') }}</h5>
        <p>{{ $this->orderAccessor->attributedTo()['name'] }}</p>
    @endif

    <div class="js-interaction">
        @if(
            $serviceCarts->isNotEmpty() ||
            $accommodationCarts->isNotEmpty() ||
            $sellableDepositCarts->isNotEmpty() ||
            $grantDepositCarts->isNotEmpty() ||
            (isset($grantProcessingFeeCarts) && $grantProcessingFeeCarts->isNotEmpty())
           )

            <h5>Services</h5>
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>{{ __('front/order.service') }}</th>
                    <th>{{ __('front/order.unit_price') }}</th>
                    <th>{{ __('ui.quantity') }}</th>
                    <th>{{ __('front/order.total_amount') }}</th>
                    <th>{{ __('front/order.total_net') }}</th>
                    <th>{{ __('front/order.total_vat') }}</th>
                    <th>{{ __('front/order.pec') }}</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @include('front.orders.user.service')
                @include('front.orders.user.deposit')
                @include('front.orders.user.grant_deposit')
                @include('front.orders.user.accommodation')

                @if(
                        $this->eventAccessor->hasNotStarted() &&
                        $accommodationCarts->reject(fn($item) => is_null($item->cancellation_request) && is_null($item->cancelled_at))->isEmpty() &&
                        $this->orderAccessor->isInvoiced() &&
                        is_null($order->amended_by_order_id) &&
                        is_null($order->amended_order_id) &&
                        $accommodationCarts->isNotEmpty()

                    )
                    <tr>
                        <th colspan="8" class="text-center pt-2">
                            <a href="{{ route('front.event.amend.accommodation.order', [
                                                'locale' =>app()->getLocale(),
                                                'event' => $order->event_id,
                                                'order' => $order->id,
                                              ]) }}" class="btn btn-sm btn-primary btn-amend-booking">
                                {{ __('front/order.amend_entire_booking') }}
                            </a>
                        </th>
                    </tr>
                @endif

                </tbody>
            </table>
        @endif
    </div>

    <div id="js_texts" class="d-none">
        <div id="cancel_order_line">{{ __('front/order.confirm_cancel_order_line') }}</div>
    </div>

    @script
    <script>

        $(document).ready(function () {
            const jContext = $('.js-interaction');

            jContext.on('click', function (e) {

                let jTarget = $(e.target);
                if (jTarget.hasClass('btn-cancel-cart-line') || jTarget.hasClass('btn-cancel-order')) {

                    let jTr = jTarget.closest('tr'),
                        container = $('#order-details'),
                        cartId = jTr.data('id'),
                        type = jTr.data('type'),
                        event_id = container.attr('data-event-id');

                    let cancellationAction = '', cancellationText;

                    if (jTarget.hasClass('btn-cancel-order')) {
                        cancellationAction = 'action=sendOrderCancellationRequest&order_id=' + container.attr('data-order-id');
                        cancellationText = $('#cancel_order').text();
                    } else {
                        cancellationAction = 'action=sendOrderCartCancellationRequest&cart_id=' + cartId + '&type=' + type;
                        cancellationText = $('#cancel_order_line').text();
                    }

                    SimpleModal.create({
                        title: 'Attention',
                        body: '<div class=\'alert alert-danger\'>' + cancellationText + '</div>',
                        showActionButton: true,
                        actionButtonText: 'Oui',
                        closeButtonText: 'Annuler',
                        onAction: function (jModal) {
                            let jSpinner = SimpleModal.getSpinner();
                            ajax(cancellationAction + '&origin=front&event_id=' + event_id, jModal, {
                                spinner: jSpinner,
                                printerOptions: {
                                    isDismissable: false,
                                },
                                successHandler: function () {
                                    SimpleModal.setBody('');
                                    SimpleModal.hideActionButton();
                                    $wire.$refresh();
                                    return true;
                                },
                            });
                        },

                    });
                    return false;
                }
            });
        });

    </script>
    @endscript

</div>
