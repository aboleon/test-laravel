<x-front-layout :event="$event">
    @php
        $mainText = \App\Helpers\Front\FrontTextHelper::getConnexionPageText($event, $registrationType);
    @endphp

    @section('class_body')
        register-page
    @endsection

    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner :type="$registrationType" class="mt-5 border-bottom"/>

    <x-mfw::response-messages/>

    <div class="container-sm text-center mt-5 fs-14">


        <div class="row text-start">
            <div class="col-12 col-md-6 order-2 order-md-1 mt-5 mt-md-0">
                <p>{{$mainText}}</p>
            </div>


            <div class="col-12 col-md-6 order-1 order-md-2">

                <h2>{{__('front/register.account_creation')}}</h2>

                @if ($errors->any())
                    <div class="text-danger mb-4 alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif


                <p class="text-tint mt-4">
                    {{  __('front/register.mail_confirm_notice') }}
                </p>


                <form class="w-100 m-auto mt-4 mt-md-5"
                      method="POST"
                      action="{{ route('front.event.registerByEmail', $event) }}"
                      novalidate
                      @submit.prevent="submitForm() && $el.submit()"
                      x-data="{
                          hasAcceptedRGPD: {{ old('has_accepted_conditions') ? 'true' : 'false' }},
                          attemptedSubmit: false,
                          submitForm() {
                              this.attemptedSubmit = true;
                              if (this.hasAcceptedRGPD) {
                                  this.$el.submit();
                              }
                          }
                      }"
                >
                    @csrf


                    <input type="hidden" name="rtype" value="{{$registrationType}}">
                    <input type="hidden" name="event_id" value="{{$event->id}}">

                    <div class="mb-3">
                        <x-mfw::input type="email"
                                      name="email"
                                      :label="__('front/register.labels.your_email_address')"
                                      :params="['placeholder' => __('front/register.labels.your_email_address_placeholder')]"
                                      value="{{old('email') }}"/>
                    </div>
                    <div class="form-check">
                        <input x-model="hasAcceptedRGPD"
                               name="has_accepted_conditions"
                               class="form-check-input"
                               type="checkbox"
                               value="1"
                               id="check-rgpd"
                               checked>
                        <label class="form-check-label small" for="check-rgpd">
                            {{__('front/register.i_accept_conditions')}}
                            <a href="#"
                               data-bs-toggle="modal"
                               data-bs-target="#modal-rgpd"
                            >{{__('front/register.the_conditions_of_use')}}</a>
                        </label>
                    </div>
                    <p x-cloak x-show="attemptedSubmit && !hasAcceptedRGPD" class="text-danger">
                        {{__('front/register.you_must_accept_conditions_to_continue')}}
                    </p>
                    <div class="form-check">
                        <input name="subscribe_newsletter"
                               class="form-check-input"
                               type="checkbox"
                               value="1"
                               id="check-newsletter">
                        <label class="form-check-label small" for="check-newsletter">
                            {{__('front/register.i_want_to_subscribe_to_the_newsletter')}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input name="subscribe_sms"
                               class="form-check-input"
                               type="checkbox"
                               value="1"
                               id="check-sms">
                        <label class="form-check-label small" for="check-sms">
                            {{__('front/register.i_want_to_subscribe_to_sms_notifications')}}
                        </label>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ \App\Accessors\EventAccessor::getEventFrontUrl($event) }}"
                           class="w-auto btn btn-secondary rounded-0">{{__('front/register.cancel')}}</a>
                        <button type="submit"
                                class="w-auto btn btn-primary rounded-0">{{__('front/register.create_my_account')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include ('front.auth.register.rgpd-modal')
</x-front-layout>
