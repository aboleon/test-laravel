@php use App\Helpers\DateHelper;use App\Helpers\Front\FrontConfigHelper; @endphp
<x-front-layout :event="$event">
    @section('class_body')
        register-page
    @endsection
    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner :type="$instance->registration_type" class="mt-5"/>


    <div class="container-sm text-center mt-5 fs-14">

        <div id="resent-registration" data-ajax="{{ route('webajax') }}">
            <x-mfw::response-messages/>
        </div>

        <div class="row text-start">
            <div class="col-12 text-center">

                <p class="fs-6 ">
                    {{__('front/register.email_not_received')}}
                </p>

                <button type="button"
                        id="resent-registration-btn"
                        class="w-auto mt-3 mt-md-0 btn btn-primary rounded-0"
                        data-event="{{ $event->id }}"
                        data-token="{{ $instance->id }}">
                    {{__('front/register.resend')}}
                </button>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(function () {
                $('#resent-registration-btn').off().click(function () {
                        ajax('action=resentFrontRegistration&event=' + $(this).attr('data-event') + '&instance=' + $(this).attr('data-token'), $('#resent-registration'));
                });
            });
        </script>
    @endpush
</x-front-layout>
