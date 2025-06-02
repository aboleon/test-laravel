@php
    use App\Accessors\Dates;
    $profile = $account->profile;
    $requestCompleted = $transport?->request_completed ?? 0;
    $page = "steps";
    if($requestCompleted){
        $page = "recap";
    }

@endphp
<x-front-logged-in-layout :event="$event">

    <x-front.session-notifs/>
    <x-front.form-errors/>

    <div x-data="{
    page: '{{$page}}',
    }">

        @include('front.user.transport.partials.divine')
        @include('front.user.transport.partials.divine-stop')

    </div>

</x-front-logged-in-layout>
