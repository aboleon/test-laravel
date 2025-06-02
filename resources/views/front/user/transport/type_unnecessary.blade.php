<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />

    <div class="container">
        <h3 class="mb-4 p-2 bg-primary-subtle rounded-1">{{__('front/transport.my_transport')}}</h3>


        <p>{{__('front/transport.you_have_previously_indicated')}}</p>


        <div class="row">
            <div class="mb-3 col-12 col-md-6">
                <div class="card">
                    <div class="card-body border">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   x-model="selectedOption"
                                   value="participant"
                                   name="flexRadioDefault"
                                   id="transport_mode1"
                                   checked
                            >
                            <label class="form-check-label fw-bold text-dark"
                                   for="transport_mode1">
                                {{__('front/transport.i_dont_need_transport')}}
                            </label>
                        </div>

                        <p class="mt-3 small">
                            {{$event->texts->transport_unnecessary}}
                        </p>

                    </div>
                </div>
            </div>
        </div>


    </div>


</x-front-logged-in-layout>
