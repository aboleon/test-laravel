<x-front-layout>

    @php
        $translations = [
            'fr' => [
                    'title' => 'Prise en charge',
                    'text1' => "pour bénéficier d'une prise en charge dans le cadre de l'évènement",
                    'text2' => "vous devez régler la caution d'un montant de",
                    'error' => "Un problème est survenu avec la génération du formulaire du paiement.",
                    'paid' => "Vous avez réglé la caution de",
                    'at' => "le",
               ],
               'en' => [
                    'title' => 'Coverage',
                    'text1' => "to benefit from coverage for the event",
                    'text2' => "you need to pay a deposit of",
                    'error' => "There was a problem generating the payment form.",
                    'paid' => "You have paid the deposit of",
                    'at' => "on",
               ]

            ];

        $isSuccess = $paymentCall->state == \App\Enum\PaymentCallState::SUCCESS->value;

    @endphp

    @if ($paymentCall->state != \App\Enum\PaymentCallState::default())
        <x-mfw::alert :type="($isSuccess ? 'success' : 'danger')"
                      class="my-5 text-center" :message="$data->paymentStateMessage()"/>


        @if ($isSuccess)
            <div class="text-center">
            <a href="{{ \App\Actions\Front\AutoConnectHelper::generateAutoConnectUrlForEventContact($paymentCall->shoppable->getEventContact()) }}" class="btn btn-dark">{{ __('front/auth.access_my_account') }}</a>
            </div>
        @endif

    @endif
    <div class="p-5 mb-4 bg-body-tertiary rounded-3 my-5">

        <div class="container-fluid py- text-center">

            <h1 class="display-6 fw-bold">{{ $translations[$accessor->locale]['title'] }}</h1>

            @if($data->status == \App\Enum\EventDepositStatus::UNPAID->value)
                <p class="fs-4"><span class="d-block text-dark fw-bold">{{ $accessor->accountNames() }},</span>
                    {{ $translations[$accessor->locale]['text1'] }} <span
                        class="d-block text-dark fw-bold">{{ $accessor->eventName() }}</span> {{ $translations[$accessor->locale]['text2'] }}
                </p>

                <h1 class="py-3">{{ \MetaFramework\Accessors\Prices::readableFormat(price:$paymentCall->total, showDecimals: false) }}</h1>


                @if ($payform)
                    {!! $payform !!}
                @else
                    <x-mfw::alert :message="$translations[$accessor->locale]['error']"/>
                @endif

            @else
                <p class="fs-4">{{ $translations[$accessor->locale]['paid'] }} {{ \MetaFramework\Accessors\Prices::readableFormat(price:$paymentCall->total, showDecimals: false) }}
                    {{ $translations[$accessor->locale]['at'] }} {{ $data->updated_at->format('d/m/Y') }} </p>
            @endif
        </div>
    </div>
</x-front-layout>
