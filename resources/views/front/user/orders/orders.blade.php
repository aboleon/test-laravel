<x-front-logged-in-layout :event="$event">
    @php
        $cart = (new \App\Accessors\Front\FrontCartAccessor())->getCart();
    @endphp

    <x-front.remaining-payments :amount="$orderAmount" :event_id="$event->id"/>

    <h4>{{ trans_choice('front/order.order', 2) }}</h4>

    <div class="container front-datatable mt-5 datatable-not-clickable">
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')

    @push('js')
        {{ $dataTable->scripts() }}
    @endpush

    @if ($attributed->isNotEmpty())

        <h4 class="mt-5">Attributions</h4>
        <x-mfw::alert type="light" :message="__('front/order.attributed_notice')" />

        <table class="table table-hover">
            <thead>
            <th class="text-dark">Type</th>
            <th class="text-dark">Libellé</th>
            <th class="text-dark">Contenu</th>
            <th class="text-dark">Origine</th>
            </thead>
            <tbody>

            @foreach($attributed as $item)
                <tr>
                    <td>{{ \App\Enum\OrderCartType::translated($item['type']) }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>{!!  $item['text']  !!}</td>
                    <td>{{ $item['attributed'] }}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    <div class="text-center js-interaction" data-event-id="{{ $event->id }}">

        @if ($eventAccessor->hasNotStarted())
            <button class="btn btn-sm btn-warning btn-cancel-order">
                {{ __('front/order.say_not_coming') }}
            </button>
        @endif
    </div>
    <div id="js_texts" class="d-none">
        <div id="cancel_order">{{ __('front/order.confirm_cancel_order') }}</div>
    </div>

    @push('js')
        <script>

            $(document).ready(function () {
                const jContext = $('.js-interaction');

                jContext.on('click', function (e) {

                    let jTarget = $(e.target);
                    if (jTarget.hasClass('btn-cancel-order')) {

                        let event_id = jContext.attr('data-event-id'),
                            cancellationAction = 'action=sendDeclinetVenutRequest',
                            cancellationText = $('#cancel_order').text();


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
                                    successHandler: function (response) {
                                        // Check if ajax_messages exists
                                        if (response.ajax_messages && response.ajax_messages.length > 0) {
                                            let successMessages = response.ajax_messages.map(function (messageObj) {
                                                // Iterate over each key-value pair in the message object
                                                return Object.keys(messageObj).map(function (key) {
                                                    let alertClass = 'alert-' + key; // Use the key as the alert class (e.g., alert-success)
                                                    let messageText = messageObj[key]; // Use the value as the message text

                                                    return '<div class="alert ' + alertClass + '">' + messageText + '</div>';
                                                }).join(''); // Join all messages into a single string
                                            }).join(''); // Join all message objects into a single string

                                            SimpleModal.setBody(successMessages); // Set all messages to modal body
                                        } else {
                                            SimpleModal.setBody('<div class="alert alert-danger">An error occurred.</div>');
                                        }
                                        SimpleModal.hideActionButton();
                                    },
                                });
                            },

                        });
                        return false;
                    }
                });
            });

        </script>
    @endpush

</x-front-logged-in-layout>
