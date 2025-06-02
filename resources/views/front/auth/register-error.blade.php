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
                <p>{{ $mainText }}</p>
            </div>
            <div class="col-12 col-md-6 order-1 order-md-2">
                <p class="fs-5 text-danger-emphasis">
                    {{ __('front/register.an_error_occurred') }}
                    @if($error)
                        <br>
                        {{ $error }}
                        <br>
                    @endif
                    {{ __('front/register.if_error_persists_contact_support') }}
                </p>
            </div>
        </div>
    </div>
</x-front-layout>
