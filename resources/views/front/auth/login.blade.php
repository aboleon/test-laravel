@php use App\Enum\RegistrationType; @endphp
<x-front-layout :event="$event->withoutRelations()">

    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner class="mt-5" :type="$registrationType" :event="$event->withoutRelations()"/>
    <x-mfw::response-messages/>

    @push('css')
        <style>
            .form-control {
                border-radius: 0 !important;
            }
        </style>
    @endpush

    @php
        $eventAccesspr = (new \App\Accessors\EventAccessor($event));
    @endphp

    <div class="text-left">
        <p class="text-start">
        @if($registrationType == RegistrationType::LOGIN->value)
            {{  __('front/auth.enter_login_credentials') }}
        @elseif($registrationType == RegistrationType::SPEAKER_DEPRECATED->value)
            {{  __('front/auth.enter_login_credentials') }}
            <p>
                {{ __('front/errors.orator_login') }}
                <x-front.event-link :event="$event" :text="__('front/ui.iam_participant')"/> {{ __('front/ui.or') }} <a
                    href="mailto:{{ $eventAccesspr->adminEmail() }}">{{ __('front/ui.contact_us') }}</a>.
            </p>

        @endif

        @if(session('not_registered_to_event'))
            @php ob_start(); @endphp
            <x-front.event-link :event="$event" :text="__('front/ui.this page')"/>
            @php $pageLink = ob_get_clean(); @endphp
            <div class="alert alert-danger">
                {!! __('front/auth.not_registered_yet_error_message', [
                'page_link' => $pageLink,
                ])!!}
            </div>
        @elseif ($errors->any())
            @if($errors->has('speakerLogin'))
                @php
                    $s = __('front/ui.the_organisation');
                    if($event->adminSubs){
                        $s = '<a href="mailto:'.$eventAccesspr->adminEmail().'">'.$eventAccesspr->adminName().'</a>';
                    }
                @endphp
                <div class="alert alert-danger">
                    {!! __('front/errors.speaker_login', ['contact' => $s]) !!}
                </div>
            @else
                <div class="alert alert-danger">
                    {!! __('front/auth.login_error_message') !!}
                </div>
            @endif
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                {!! session('status') !!}
            </div>
        @endif


        <form class="w-50 m-auto mt-4 mt-md-5"
              method="POST"
              action="{{ route('front.event.login', $event) }}">
            @csrf


            <input type="hidden" name="registration_type" value="{{  $registrationType}} ">

            <div class="row mb-3 align-items-center">
                <label for="inputEmail3"
                       class="col-md-5 col-xl-4 col-form-label text-start">{{__('front/auth.your_email')}}</label>
                <div class="col-md-7 col-xl-8">
                    <x-mfw::input type="email"
                                  name="email"
                                  value="{{old('email') ?: session('registration_email')}}"/>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
                <label for="inputPassword3"
                       class="col-md-5 col-xl-4 col-form-label text-start">{{__('front/auth.your_password')}}</label>
                <div class="col-md-7 col-xl-8">
                    <x-mfw::input type="password" name="password"/>
                </div>
            </div>
            <div class="text-end mb-3">

                <button type="submit"
                        class="w-auto mt-3 mt-md-0 btn btn-lg btn-primary rounded-0">{{__('front/auth.login')}}</button>
            </div>
            <hr>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                <a class="w-auto mb-1 mb-md-0 text-tint text-decoration-none"
                   href="{{route('front.event.password.request', [
                            'event' => $event,
                            'rtype' => $registrationType ?: RegistrationType::default(),
                       ])}}">{{__('front/auth.forgot_password')}}</a>


                <div>
                    @if(in_array($registrationType, RegistrationType::defaultGroups()))
                        <a class="btn btn-sm btn-warning rounded-0" href="{{route('front.event.register', [
                        'event' => $event,
                        'rtype' => $registrationType,
                    ])}}">{{__('front/auth.create_an_account')}}</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <x-front.specific-text-login :type="$registrationType" :event="$event"/>


</x-front-layout>
