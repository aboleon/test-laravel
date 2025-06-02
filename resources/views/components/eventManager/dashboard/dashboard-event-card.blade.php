@php use App\Accessors\EventAccessor;use App\Models\GenericMedia; @endphp
@props([
    "containerClass" => "",
    "event" => null,
    "families" => null,
])
@php
    $frontEventUrl = EventAccessor::getEventFrontUrl($event);
    $defaultImg = Mediaclass::ghostUrl(GenericMedia::class, 'banner_medium');
@endphp
<div class="{{$containerClass}} mb-4">
    <div data-href="{{ route('panel.manager.event.show', $event) }}"
         class="event-link text-decoration-none">
        <div class="card text-start">
            <x-front.event-banner :event="$event->withoutRelations()" group="banner_medium" :default="$defaultImg"/>
            <div class="card-body">
                <div class="row align-items-start">
                    <div class="col-sm-8">
                        @if(array_key_exists($event->event_main_id, $families))
                            <h6 class="event-family fs-6 fw-bold p-0 m-0">{{ $families[$event->event_main_id] }}</h6>
                        @endif
                        <h5 class="event-name p-0 m-0">
                            {{ $event->texts->subname }}</h5>
                    </div>
                    <div class="col-sm-4 text-end">
                        <p class="event-dates smaller p-0 m-0">{{ $event->starts .' - '. $event->ends }}</p>
                        <p class="small p-0 m-0"><a href="{{$frontEventUrl}}"
                                                    target="_blank"
                                                    class="front-event-link">lien front</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push("css")
    <style>
        .event-link {
            cursor: pointer;
        }
    </style>
@endpush
@pushonce("js")
    <script>
        $(document).ready(function () {
            $('.event-link').click(function (e) {

                let jTarget = $(e.target);
                if (jTarget.hasClass('front-event-link')) {
                    // just let the link do its job
                } else {
                    window.location.href = $(this).data('href');
                    return false;
                }
            });
        });
    </script>
@endpushonce
