@php
    $requestCompleted = $transport?->request_completed ?? 0;
    $page = "start";
    if($step>0 || $errors->isNotEmpty()){
        $page = "participant";
    }
    if($requestCompleted){
        $page = "recap";
    }
    if(0 === $step){
        $step=1;
    }


@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />
    <x-front.form-errors />

    <div x-data="{
    page: '{{$page}}',
    }">


        @include('front.user.transport.partials.participant-start')
        @include('front.user.transport.partials.participant')
        @include('front.user.transport.partials.participant-stop')
    </div>

</x-front-logged-in-layout>
