@php
   $nbSteps = 5;
   if(!$isTransferEligible) {
       $nbSteps = 4;
   }
@endphp


<div x-show="'participant' === page"
     x-cloak
     x-data="{step: {{$step}}}"
>


    @include ('front.user.transport.partials.interventions')

    <div class="transport-page">


        <div>
            <h1 x-show="step === 1" class="mt-3">{{__('front/transport.departure_info')}}</h1>
            <h1 x-show="step === 2" class="mt-3">{{__('front/transport.return_info')}}</h1>
            <h1 x-show="step === 3" class="mt-3">{{__('front/transport.invoices')}}</h1>
            @if($isTransferEligible)
                <h1 x-show="step === 4" class="mt-3">{{__('front/transport.transfer')}}</h1>
            @endif
            <h1 x-show="step == 5" class="mt-3">{{__('front/transport.summary')}}</h1>


            <div id="stepper" class="bs-stepper stepper-outline mt-0 mt-md-3">
                <div class="card-header bg-light border-bottom px-lg-5">
                    @include('front.user.transport.partials.step-header')
                </div>


                <div class="bs-stepper-content px-0">
                    <div x-show="step === 1" x-transition>
                        @include('front.user.transport.partials.participant-step-departure')
                    </div>
                    <div x-show="step === 2" x-transition>
                        @include('front.user.transport.partials.participant-step-return')
                    </div>
                    <div x-show="step === 3" x-transition>
                        @include('front.user.transport.partials.transport-participant-step-documents')
                    </div>

                    @if($isTransferEligible)
                        <div x-show="step == 4" x-transition>
                            @include('front.user.transport.partials.participant-step-transfer')
                        </div>
                    @endif

                    <div x-show="{{$nbSteps}} === step" x-transition>
                        @include('front.user.transport.partials.participant-step-recap')
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@pushonce('css')
    <link rel="stylesheet" href="{{ asset('vendor/bs-stepper/bs-stepper.min.css') }}">
@endpushonce
