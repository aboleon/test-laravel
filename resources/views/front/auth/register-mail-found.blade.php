<x-front-layout :event="$event">
    @section('class_body')
        register-page
    @endsection

    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner :type="$registrationType" class="mt-5" />


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
                    {{__('front/register.email_already_exist_for_this_congress')}}
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                    <a class="w-auto mb-1 mb-md-0 text-tint text-decoration-none"
                       href="{{route('front.event.password.request', $event)}}">{{__('front/register.forgotten_password')}}</a>
                    <a href="{{route('front.event.login', [
                        'event' => $event,
                        'rtype' => $registrationType,
                    ])}}" class="w-auto mt-3 mt-md-0 btn btn-primary rounded-0">{{__('front/register.connect')}}</a>
                </div>
            </div>
        </div>
    </div>
</x-front-layout>
