<x-front-logged-in-layout :event="$event">
    <h3 class="p-2 ps-0 divine-main-color-text rounded-1">Commandes à finaliser</h3>


    @if(session("return_type"))
        @if(session("return_type") == 'success')
            <div class="alert alert-success">
                <p>
                    {{ __('front/order.was_paid') }}
                    <br>
                    {!! __('front/order.go_to_order', ['route' => route('front.event.orders.index', [
                        'event' => $event->id,
                        'locale' => app()->getLocale()
                    ])]) !!}
                </p>
            </div>
        @else
            <div class="alert alert-danger">
                <p>
                    {{ __('front/order.payment_error') }}
                    <br>
                    {{ __('front/order.retry_or_contact_support') }}
                </p>
            </div>
        @endif
    @endif

    @if ($has_any_orders)



        @if($remainingOrders->isNotEmpty())
            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('ui.date') }}</th>
                    <th>{{ __('front/order.amount_total') }}</th>
                    <th>{{ __('front/order.amount_to_pay') }}</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="remaining-orders-table">
                <x-remaining-orders-front :orders="$remainingOrders"/>
                </tbody>
            </table>
        @endif

        @if ($assignedOrders->isNotEmpty())

            <p class="text-dark fw-bold">
                {{ __('front/order.assigned_payer') }}
            </p>

            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('ui.date') }}</th>
                    <th>{{ __('front/order.amount_total') }}</th>
                    <th>{{ __('front/order.amount_to_pay') }}</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="assigned-orders-table">
                <x-remaining-orders-front :orders="$assignedOrders"/>
                </tbody>
                @endif
            </table>

            @include('front.user.remaining-payments.remaining-payments-confirm-modal')


            @push("js")
                <script>
                    $(document).ready(function () {

                        let orderId = null;
                        const jModal = $('#remainingPaymentsConfirmModal');

                        const jSelectPayment = $('.action-select-remaining-payment');
                        jSelectPayment.on('click', function () {
                            orderId = $(this).closest('tr').data('order-id');
                            let action = 'action=getPayboxForm&order_id=' + orderId + "&event_contact_id={{$eventContact->id}}";
                            ajax(action, jModal, {
                                successHandler: function (r) {
                                    let s = r.payboxFormBegin;
                                    s += '<input type="submit" class="btn btn-primary" value="Poursuivre">';
                                    s += r.payboxFormEnd;
                                    jModal.find('.continue-button-container').html(s);
                                },
                            });
                        });

                    });
                </script>
            @endpush

        @else
            <x-mfw::alert type="info" :message="__('front/dashboard.no_remaining_payments')"/>
        @endif

</x-front-logged-in-layout>
