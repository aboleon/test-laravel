<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs/>

    <form action="{{route('front.event.account.update', $event->id )}}"
          method="post"
          enctype="multipart/form-data"
          class="fs-14 account-container">
        @csrf
        @method('PUT')

        <x-front.form-errors/>


        <x-mediaclass::uploadable :model="$account"
                                  :settings="['group'=>'avatar']"
                                  size="small"
                                  :limit="1"
                                  :description="false"
                                  label="Ajouter une photo de profil"/>

        <livewire:front.user.info-section :account="$account"
                                          :eventContact="$eventContact"
                                          :event="$event"
                                          :show-participation-type="false"
                                          context="account"
                                          :domains="$domains"
                                          :registrationType="$eventContact->registration_type"/>

        <div class="d-flex justify-content-end gap-1 mt-2 mb-5">
            <button type="submit"
                    class="btn btn-primary rounded-0 fs-14">{{__('front/ui.confirm')}}</button>
            <button class="btn bg-gray rounded-0 fs-14">{{__('front/ui.cancel')}}</button>
        </div>
    </form>


</x-front-logged-in-layout>
