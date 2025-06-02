@props([
    'type' => 'default',
    'event' => null,
])

@if($event)
    <div class="w-50 m-auto mt-4 mt-md-5 text-left">
            @switch($type)
                @case('individual')
                @case('congress')
                @case('participant')
                    @if($event && $event->texts->second_fo_login_participant)
                        <div class="mt-2">{!! $event->texts->second_fo_login_participant !!}</div>
                    @endif
                @break
                @case('industry')
                    @if($event && $event->texts->second_fo_login_industry)
                        <div class="mt-2">{!! $event->texts->second_fo_login_industry !!}</div>
                    @endif
                @break
                @case('orator')
                @case('speaker')
                    @if($event && $event->texts->second_fo_login_speaker)
                        <div class="mt-2">{!! $event->texts->second_fo_login_speaker !!}</div>
                    @endif
                @break
                @case('group')
                    @if($event && $event->texts->second_fo_exhibitor)
                        <div class="mt-2">{!! $event->texts->second_fo_exhibitor !!}</div>
                    @endif
                @break
            @endswitch

            <div class="mt-2 d-flex justify-content-end">
                <a href="{{ url()->previous() }}" class="btn btn-secondary rounded-0">{{ __('mfw.goback') }}</a>
            </div>


    </div>
@endif
