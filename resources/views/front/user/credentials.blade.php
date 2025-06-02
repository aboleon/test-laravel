<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />


    <form action="{{route('front.event.credentials.update', $event)}}"
          method="post"
          enctype="multipart/form-data"
          class="fs-14 account-container">
        @csrf
        @method('PUT')


        <x-front.form-errors />


        @include ('front.user.credentials.credentials-card')

        <div class="d-flex justify-content-end gap-1 mt-2 mb-5">
            <button type="submit"
                    class="btn btn-primary rounded-0 fs-14">{{__('front/ui.confirm')}}</button>
            <button class="btn bg-gray rounded-0 fs-14">{{__('front/ui.cancel')}}</button>
        </div>

    </form>



</x-front-logged-in-layout>
