@php
    $page = "'divine'";
    $page = 'null';
@endphp
<x-front-logged-in-layout :event="$event">

    <x-front.session-notifs/>

    <div class="container"
         x-data="{selectedOption: '', clickedButton: false, showDetails: false, page: {{$page}}}">


        <div x-show="(true === clickedButton && '' === selectedOption) || (false === showDetails && !page)"
             x-cloak>

            <h3 class="mb-4 p-2 divine-main-color-text zzbg-primary-subtle rounded-1">{{__('Mon transport')}}</h3>
            <div x-show="true === clickedButton && '' === selectedOption " x-cloak>
                <div class="alert alert-danger">{{__('front/transport.select_an_option')}}</div>
            </div>
            <div class="row">
                <div class="mb-3 col-12 col-md-6">
                    <div class="card">
                        <div class="card-body border">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       x-model="selectedOption"
                                       value="{{ \App\Enum\DesiredTransportManagement::PARTICIPANT->value }}"
                                       name="flexRadioDefault"
                                       id="transport_mode1"
                                >
                                <label class="form-check-label fw-bold text-dark"
                                       for="transport_mode1">
                                    {{__('front/transport.i_manage_my_transport')}}
                                </label>
                            </div>
                            @if($event->texts->transport_user)
                                <p class="mt-3 small">
                                    {{ $event->texts->transport_user }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mb-3 col-12 col-md-6">
                    <div class="card">
                        <div class="card-body border">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       x-model="selectedOption"
                                       value="{{ \App\Enum\DesiredTransportManagement::DIVINE->value }}"
                                       name="flexRadioDefault"
                                       id="transport_mode2">
                                <label class="form-check-label fw-bold text-dark"
                                       for="transport_mode2">
                                    {{__('front/transport.organization_manages_transport')}}
                                </label>
                            </div>

                            @if($event->texts->transport_admin)
                                <p class="mt-3 small">
                                    {{ $event->texts->transport_admin }}
                                </p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-12 col-md-6">
                    <div class="card">
                        <div class="card-body border">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       x-model="selectedOption"
                                       value="{{ \App\Enum\DesiredTransportManagement::UNNECESSARY->value }}"
                                       name="flexRadioDefault"
                                       id="transport_mode3">
                                <label class="form-check-label fw-bold text-dark"
                                       for="transport_mode3">
                                    {{__('front/transport.i_dont_need_transport')}}
                                </label>
                            </div>

                            @if ($event->texts->transport_unnecessary)
                                <p class="mt-3 small">
                                    {{ $event->texts->transport_unnecessary }}
                                </p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-end">
                <button @click="showDetails=true; clickedButton=true"
                        class="btn btn-primary w-auto">{{__('front/transport.next_step')}}
                </button>
            </div>
        </div>

        <div x-cloak x-show="showDetails && 'participant' === selectedOption">
            <div class="row">
                <div class="card">
                    <div class="card-body border">
                        <h5 class="d-block my-3">{{__('front/transport.i_manage_my_transport')}}</h5>

                        {{$event->texts->transport_user}}
                    </div>
                </div>
                @if($event->texts->max_price_text)
                    <p class="text-warning-emphasis p-2 mt-3 text-md-center">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{$event->texts->max_price_text}}
                    </p>
                @endif
            </div>
            <div class="d-flex flex-wrap justify-content-start justify-content-md-center gap-2 mt-3">
                <button @click="showDetails=false"
                        class="btn btn-sm btn-secondary w-auto">{{__('front/transport.back_to_choices')}}
                </button>

                <form action="{{route("front.event.transport.update", $event)}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="desired_management"
                           value="{{ \App\Enum\DesiredTransportManagement::PARTICIPANT->value }}"/>
                    <button type="submit" class="btn btn-sm btn-primary w-auto">
                        {{__('ui.save')}}
                    </button>
                </form>
            </div>

            <p class="text-danger mt-3 text-start text-md-center">
                @if($event->transport_tickets_limit_date)
                    {{__('front/transport.you_have_until_x_to_submit_your_ticket', ["date" => $event->transport_tickets_limit_date])}}
                    <br>
                @endif
                {{__('front/transport.you_will_not_be_able_to_change_your_choice_later')}}
            </p>
        </div>

        <div x-cloak x-show="showDetails && 'divine' === selectedOption">
            <div class="row">
                <div class="card">
                    <div class="card-body border">
                        <h5 class="mt-3">{{__('front/transport.organization_manages_transport')}}</h5>
                        <p class="mt-3">
                            {{$event->texts->transport_admin}}
                        </p>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-start justify-content-md-center gap-2 mt-4">
                <button @click="showDetails=false"
                        class="btn btn-sm btn-secondary w-auto">{{__('front/transport.back_to_choices')}}
                </button>
                <button @click="showDetails=false"
                        class="d-inline d-md-none btn smaller btn-sm btn-primary w-auto">{{__('front/transport.organization_manages_transport')}}
                </button>
                <form action="{{route("front.event.transport.update", $event)}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="desired_management" value="divine"/>
                    <button type="submit"
                            class="btn btn-sm btn-primary w-auto">{{__('front/transport.organization_manages_transport')}}
                    </button>
                </form>
            </div>

            <p class="text-danger mt-3 text-start text-md-center">
                {{__('front/transport.you_will_not_be_able_to_change_your_choice_later')}}
            </p>
        </div>

        <div x-cloak x-show="showDetails && '{{ \App\Enum\DesiredTransportManagement::UNNECESSARY->value }}' === selectedOption">
            <div class="row">
                <div class="card">
                    <div class="card-body border">
                        <h5 class="mt-3">{{__('front/transport.i_dont_need_transport')}}</h5>
                        <p class="mt-3">
                            {{$event->texts->transport_unnecessary}}
                        </p>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-start justify-content-md-center gap-2 mt-4">
                <button @click="showDetails=false"
                        class="btn btn-sm btn-secondary w-auto">{{__('front/transport.back_to_choices')}}
                </button>

                <form action="{{route("front.event.transport.update", $event)}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="desired_management" value="unnecessary"/>
                    <button type="submit" class="btn btn-sm btn-primary w-auto">
                        {{__('front/transport.i_dont_need_transport')}}</button>
                </form>
            </div>

            <p class="text-danger mt-3 text-start text-md-center">
                {{__('front/transport.you_will_not_be_able_to_change_your_choice_later')}}
            </p>
        </div>


    </div>


</x-front-logged-in-layout>
