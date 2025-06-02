@php use App\Accessors\EventAccessor; @endphp
<x-front-layout :event="$event">
    @section('class_main')
        event
    @endsection

    @php
        $mediaUrl = EventAccessor::getBannerUrlByEvent($event);
    @endphp

    <x-front.event-banner :event="$event->withoutRelations()"/>

    <x-mfw::response-messages/>


    <div @class([
      'header',
      'text-center',
      'mt-4',
    ])>

        <h1 class="title fs-4 divine-main-color-text">{{$event->texts->name}}</h1>
        <div class="date fs-5">
            {{$event->starts}}
            -
            {{$event->ends}}
        </div>
    </div>

    <div class="description mt-4 text-left">
        {!! $event->texts->description !!}
    </div>


    <div class="registration mt-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-xl-4">
                    <div
                        class=" border border-black event-registration-block bg-opacity-10 rounded-3 text-center p-3 position-relative btn-transition">
                        <h5 class="mb-1">
                            <a href="{{route('front.event.login', [
                        "event" => $event->id,
                        "rtype" => "participant",
                        ])}}"
                               class="stretched-link divine-secondary-color-text">{{__('front/event.i_am_participant')}}</a>
                        </h5>
                        <span class="mb-0">
                        {{__('front/event.i_am_participant_description')}}
                        </span>
                        @if($event->texts->second_fo_particpant_subtitle)
                            <h6 class="mt-3">{{ $event->texts->second_fo_particpant_subtitle }}</h6>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div
                        class="border border-black event-registration-block bg-opacity-10 rounded-3 text-center p-3 position-relative btn-transition">
                        <h5 class="mb-1">
                            <a href="{{route('front.event.login', [
                        "event" => $event->id,
                        "rtype" => "industry",
                        ])}}"
                               class="stretched-link divine-secondary-color-text">{{__('front/event.i_am_industry')}}</a>
                        </h5>
                        <span class="mb-0">{{__('front/event.i_am_industry_description')}}</span>
                        @if($event->texts->second_fo_industry_subtitle)
                            <h6 class="mt-3">{{ $event->texts->second_fo_industry_subtitle }}</h6>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div
                        class="border border-black event-registration-block bg-opacity-10 rounded-3 text-center p-3 position-relative btn-transition">
                        <h5 class="mb-1">
                            <a href="{{route('front.event.login', [
                        "event" => $event->id,
                        "rtype" => "speaker",
                        ])}}"
                               class="stretched-link divine-secondary-color-text">{{__('front/event.i_am_speaker')}}</a>
                        </h5>
                        <span class="mb-0">{{__('front/event.i_am_speaker_description')}}</span>
                        @if($event->texts->second_fo_speaker_subtitle)
                            <h6 class="mt-3">{{ $event->texts->second_fo_speaker_subtitle }}</h6>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div
                        class="border border-black event-registration-block bg-opacity-10 rounded-3 text-center p-3 position-relative btn-transition">
                        <h5 class="mb-1">
                            <a href="{{route('front.event.login', [
                        "event" => $event->id,
                        "rtype" => "group",
                        ])}}"
                               class="stretched-link divine-secondary-color-text">{{__('front/event.i_am_group')}}</a>
                        </h5>
                        <span class="mb-0">{{__('front/event.i_am_group_description')}}</span>
                        @if($event->texts->second_fo_exhibitor_subtitle)
                            <h6 class="mt-3">{{ $event->texts->second_fo_exhibitor_subtitle }}</h6>
                        @endif
                    </div>
                </div>


                @if(false)
                    <div class="col-md-6 col-xl-4">
                        <div
                            class="border border-black event-registration-block bg-opacity-10 rounded-3 text-center p-3 position-relative btn-transition">
                            <h5 class="mb-1"><a href="{{route('front.event.register', [
                        "event" => $event->id,
                        "rtype" => "sponsor",
                        ])}}"
                                                class="stretched-link divine-secondary-color-text">{{ __('front/ui.manage_stand') }}</a>
                            </h5>
                            <span class="mb-0"></span>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a class="btn btn-primary already-registered p-3"
           href="{{route('front.event.login', $event->id)}}">{{__('front/event.already_registered')}}</a>
    </div>

    @if($event->texts->second_home_subtitle)
        <div class="text-center mt-5">
            <h4>{{ $event->texts->second_home_subtitle }}</h4>
            <div>{!! $event->texts->second_fo_home !!}</div>
        </div>
    @endif

</x-front-layout>
