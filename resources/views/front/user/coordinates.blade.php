@php
    $account = $user->account;
@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs/>
    <div>
        <livewire:front.user.mail-section :user="$user" :account="$account"/>
    </div>
    <div>
        <livewire:front.user.phone-section :user="$user" :account="$account"/>
    </div>
    <div>
        <livewire:front.user.address-section :account="$account"/>
    </div>


</x-front-logged-in-layout>
