<x-front-layout :event="$event">
    @section('class_body')
        register-page
    @endsection
    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner :type="$registrationType" class="mt-5"/>

    <div class="container-sm text-center mt-5 fs-14">
        <div class="row text-start">
            <div class="col-12 col-md-6 order-2 order-md-1 mt-5 mt-md-0">
                @php
                    $mainText = \App\Helpers\Front\FrontTextHelper::getConnexionPageText($event, $registrationType);
                @endphp
                <p>{{$mainText}}</p>
            </div>


            <div class="col-12 col-md-6 order-1 order-md-2">
                <p class="fs-5 text-danger-emphasis">
                    {{__('front/register.thanks_for_validating_your_email')}}
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                    <a href="{{route('front.register-public-account-form', [
                            'locale' => app()->getLocale(),
                            'token' => $token
                        ])}}" class="w-auto mt-3 mt-md-0 btn btn-primary rounded-0">
                        {{__('front/register.complete_the_registration_process')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-front-layout>
