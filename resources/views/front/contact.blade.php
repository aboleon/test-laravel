@php
    use App\Accessors\EventAccessor;
    use App\Accessors\Settings;
@endphp
<x-front-layout :event="$event">
    @section('class_main')
        event
    @endsection

    @php
        $mediaUrl = EventAccessor::getBannerUrlByEvent($event);
        $texts = $event->texts;
        $adminSharedAddress = Settings::getValue("admin_shared_address");
        $admins = [];
        if($event->admin){
            $admins[__("front/event.contact_event_manager")] = $event->admin;
        }
        if($event->adminSubs){
            $admins[__("front/event.contact_registration_manager")] = $event->adminSubs;
        }
        if($event->pec){
            if($event->pec->admin){
                $admins[__("front/event.contact_industry_grants_registration_manager")] = $event->pec->admin;
            }
            if($event->pec->grantAdmin){
                $admins[__("front/event.contact_grants_and_subsidies_manager")] = $event->pec->grantAdmin;
            }
        }
    @endphp

    <x-front.event-banner :event="$event->withoutRelations()"/>


    <div @class([
      'header',
      'text-center',
      'mt-4',
    ])>

        <h1 class="title fs-4">{{$event->texts->name}}</h1>
        <div class="date fs-5">
            {{$event->starts}}
            -
            {{$event->ends}}
        </div>
    </div>


    <hr>
    <div class="container">
        <h1 class="text-center">{{$texts->contact_title}}</h1>
        <p class="text-start">
            {{$texts->contact_text}}
        </p>
    </div>
    @if($admins)
        <hr>
        <div class="container">
            <h5>{{__('front/event.contact_organization')}}</h5>
            @if($adminSharedAddress)
                <i class="bi bi-geo-alt-fill"></i> {{$adminSharedAddress}}<br>
            @endif

            <hr>
            <div class="row g-2">
                @foreach($admins as $title => $admin)
                    @php

                        $profile = $admin->profile;

                        $job = $profile?->job;
                        $phone = $profile?->phone;
                        $mobile = $profile?->mobile;
                        $photoUrl = \App\Accessors\Accounts::getPhotoByAccount($admin);

                    @endphp
                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                        <div class="card border p-2" style="max-width: 540px;">
                            <div class="mt-2 ms-2">
                                <p class="card-text text-break text-instagram fw-bold fs-6">{{$title}}</p>
                            </div>
                            <hr>
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <img src="{{$photoUrl}}"
                                         class="img-fluid rounded-circle divine-user-photo"
                                         alt="divine-user">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <h5 class="card-title">{{$admin->names()}}</h5>
                                        @if($job)
                                            <p class="card-text">{{$job}}</p>
                                        @endif

                                        <p class="card-text">
                                            @if($phone)
                                                <i class="bi bi-telephone me-2"></i>
                                                <a href="tel:{{$phone}}">{{$phone}}</a><br>
                                            @endif
                                            @if($mobile)
                                                <i class="bi bi-phone me-2"></i>
                                                <a href="tel:{{$mobile}}">{{$mobile}}</a>
                                                <br>
                                            @endif
                                            <i class="bi bi-envelope me-2"></i>
                                            <a href="mailto:{{$admin->email}}">{{$admin->email}}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>



        @push("css")
            <style>

                .divine-user-photo {
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                }
            </style>
        @endpush
</x-front-layout>
