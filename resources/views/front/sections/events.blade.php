@php
    use App\Accessors\EventAccessor;use App\Models\GenericMedia;
@endphp
    <!-- =======================
    Events START -->
<section>

    <div class="container">
        @if(is_string(session('genericError')) && !empty(session('genericError')))
            <div class="alert alert-danger">
                {{ session('genericError')}}
            </div>
        @endif
        <!-- Title -->
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="fs-1">{{__("front/home.browse_our_events")}}</h2>
                <p class="mb-0">{{__('front/home.browse_our_events_description')}}</p>
            </div>
        </div>


        <!-- Tabs content START -->
        <div class="tab-content" id="course-pills-tabContent">
            <!-- Content START -->
            <div class="tab-pane fade show active"
                 id="course-pills-tabs-1"
                 role="tabpanel"
                 aria-labelledby="course-pills-tab-1">
                <div class="row g-4">
                    @php
                        // TODO : Disable past events
                        $events = \App\Models\Event::with(["texts", "media"])->published()->get();
                        $defaultThumbnail = Mediaclass::ghostUrl(GenericMedia::class, 'thumbnail');
                    @endphp
                    @foreach($events as $event)
                        @php
                            $mediaUrl = EventAccessor::getBannerUrlByEvent($event);
                            $url = EventAccessor::getEventFrontUrl($event);
                        @endphp

                            <!-- Card item START -->
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <div class="card shadow h-100">
                                <!-- Image -->
                                <x-mediaclass::printer :model="Mediaclass::forModel($event, 'thumbnail')->first()"
                                                       :alt="$event->texts->name"
                                                       class="img-fluid"
                                                       :default="$defaultThumbnail"
                                                       :responsive="false"/>
                                <!-- Card body -->
                                <div class="card-body pb-0">
                                    <!-- Title -->
                                    <h5 class="card-title fw-normal"><a href="{{$url}}"
                                                                        class="stretched-link">{{$event->texts->name}}</a>
                                    </h5>
                                    <p class="mb-2 text-truncate-2">
                                        {{$event->starts}}
                                        -
                                        {{$event->ends}}
                                    </p>

                                </div>
                            </div>
                        </div>
                        <!-- Card item END -->
                    @endforeach

                </div> <!-- Row END -->
            </div>
            <!-- Content END -->

        </div>
        <!-- Tabs content END -->
    </div>
</section>
<!-- =======================
Events END -->
