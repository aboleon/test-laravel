<x-front-layout :event="$event">

    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner class="mt-5" type="login"/>
    @push('css')
        <style>
            .form-control {
                border-radius: 0 !important;
            }
        </style>
    @endpush
    <div class="text-left">
        <p>
            {!! __('front/auth.forgot_password_message') !!}
        </p>


        @if ($errors->any())
            <div class="alert alert-danger">
                @if ($errors->get('event'))
                    {!! str_replace('#', route('front.home') ,__('front/auth.forgot_password_no_event_error_message')) !!}
                @else
                    {{__('front/auth.email_not_found_notif')}}
                @endif
            </div>
        @endif


        <form class="w-50 m-auto mt-4 mt-md-5"
              method="POST"
              action="{{ route('front.event.password.email', $event) }}">
            @csrf
            <div class="row mb-3 mb-sm-4 align-items-center">
                <label for="inputEmail3"
                       class="col-md-5 col-xl-4 col-form-label text-start">{{__('front/auth.your_email')}}</label>
                <div class="col-md-7 col-xl-8">
                    <x-mfw::input type="email"
                                  name="email"
                                  value="{{old('email') }}"/>
                </div>
            </div>
            <div class="mb-3">
                <button type="submit"
                        class="w-auto btn btn-primary rounded-0">{{__('front/auth.reset_password')}}</button>
            </div>
            <div class="text-start">
                @php
                    if($registrationType == \App\Enum\RegistrationType::SPEAKER->value){
                        $registrationType = \App\Enum\RegistrationType::default();
                    }
                @endphp
                <u><i><a href="{{route('front.event.register', [
                        'event' => $event,
                        'rtype' => $registrationType ?? \App\Enum\RegistrationType::default(),
                    ])}}">{{__('front/auth.create_an_account')}}</a></i></u>
            </div>
        </form>
    </div>

</x-front-layout>
