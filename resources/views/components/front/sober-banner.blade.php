@props([
    'type' => 'default',
    'backlink' => null,
])

<div {{ $attributes->merge(['class' => 'sober-banner position-relative pb-1']) }}>

    @if($backlink)
        <a href="{{ $backlink }}" class="backlink p-1 position-absolute top-0 end-0 mt-sm-1 me-sm-1 ps-sm-2 pe-sm-2">
            {{__('front/event.back')}}
        </a>
    @endif

    <h1 class="divine-main-color-text">
        @switch($type)
            @case('login')
                {{__('front/auth.login_to_my_account')}}
                @break
            @case('create-account')
            @case('register')
                {{__('front/auth.register')}}
                @break
            @case('individual')
            @case('congress')
            @case('participant')
                <b>{{__('front/event.i_am_participant')}}</b>
                @break
            @case('industry')
                <b>{{__('front/event.i_am_industry')}}</b>
                @break
            @case('orator')
            @case('speaker')
                <b>{{__('front/event.i_am_speaker')}}</b>
                @break
            @case('group')
                <b>{{__('front/event.i_am_group')}}</b>
                @break
            @case('sponsor')
                <b>Je gère mon stand</b>
                @break
            @case('dashboard')
                {{__('front/dashboard.my_account_title')}}
                @break
            @case('account')
                {{__('front/account.my_personal_info')}}
                @break
            @case('to_set')
                {{__('account.create')}}
                @break
            @default
                {{__('front/home.choose_next_congress')}}
                @break
        @endswitch
    </h1>
</div>
