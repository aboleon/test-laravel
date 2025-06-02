@php
    if($step == 0) {
        $step = 1;
    }

   $nbSteps = 5;
   if(!$isTransferEligible){
       $nbSteps = 4;
   }
@endphp
<div
        x-data="{step: {{$step}}}"
        x-cloak
        x-show="page === 'steps'"
>


    <h1 x-show="step == 5" class="mt-3 mb-3">
        {{__('front/transport.summary')}}
    </h1>
    @include ('front.user.transport.partials.interventions')

    <div class="transport-page">


        <div>
            <h1 x-show="step != 5" class="mt-3">
                {{__('front/transport.transport_request')}}
            </h1>


            <div id="stepper" class="bs-stepper stepper-outline mt-0 mt-md-3">
                <div class="card-header bg-light border-bottom px-lg-5">
                    @include('front.user.transport.partials.step-header')
                </div>


                <div class="bs-stepper-content px-0">
                    <div x-show="1 === step" x-transition>
                        @include('front.user.transport.partials.divine-step-info')
                    </div>
                    <div x-show="2 === step" x-transition>
                        @include('front.user.transport.partials.divine-step-departure')
                    </div>
                    <div x-show="3 === step" x-transition>
                        @include('front.user.transport.partials.divine-step-return')
                    </div>
                    @if($isTransferEligible)
                        <div x-show="4 === step" x-transition>
                            @include('front.user.transport.partials.divine-step-transfer')
                        </div>
                    @endif
                    <div x-show="{{$nbSteps}} === step" x-transition>
                        @include('front.user.transport.partials.divine-step-recap')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@pushonce('css')
    <link rel="stylesheet" href="{{ asset('vendor/bs-stepper/bs-stepper.min.css') }}">
@endpushonce
