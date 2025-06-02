@php
    $account = $user->account;
@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />
    <livewire:front.user.identity-card-section :account="$account" />


</x-front-logged-in-layout>
