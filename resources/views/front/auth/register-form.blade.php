@php
    use Illuminate\Support\Js;
@endphp
<x-front-layout :event="$event">

    @section('class_body')
        register-page
    @endsection

    <x-front.event-banner :event="$event->withoutRelations()"/>
    <x-front.sober-banner :type="$registrationType" class="mt-5"/>

    <div x-data="{
            step: {{ $stepNumber }},
            maxStepNumber: {{ $maxStepNumber }},
        }">

        <div id="stepper" class="bs-stepper stepper-outline mt-0 mt-md-3">
            <div class="card-header bg-light border-bottom px-lg-5">
                <div class="bs-stepper-header" role="tablist">
                    <div class="step" :class="{ 'active': step === 1 }" @click="step=1">
                        <div class="d-grid text-center align-items-center">
                            <button type="button"
                                    class="btn btn-link step-trigger mb-0">
                                <span class="bs-stepper-circle">1</span>
                            </button>
                        </div>
                    </div>
                    <div class="line"></div>
                    <div class="step"
                         :class="{ 'active': step === 2 }"
                         @click="if(maxStepNumber >= 2) step=2">
                        <div class="d-grid text-center align-items-center">
                            <button type="button"
                                    class="btn btn-link step-trigger mb-0">
                                <span class="bs-stepper-circle">2</span>
                            </button>
                        </div>
                    </div>
                    <div class="line"></div>
                    <div class="step"
                         :class="{ 'active': step === 3 }"
                         @click="if(maxStepNumber >= 3) step=3">
                        <div class="d-grid text-center align-items-center">
                            <button type="button"
                                    class="btn btn-link step-trigger mb-0">
                                <span class="bs-stepper-circle">3</span>
                            </button>
                        </div>
                    </div>
                    <div class="line"></div>
                    <div class="step"
                         :class="{ 'active': step === 4 }"
                         @click="if(maxStepNumber >= 4) step=4">
                        <div class="d-grid text-center align-items-center">
                            <button type="button"
                                    class="btn btn-link step-trigger mb-0">
                                <span class="bs-stepper-circle">4</span>
                            </button>
                        </div>
                    </div>
                    <div class="line"></div>
                    <div class="step"
                         :class="{ 'active': step === 5 }"
                         @click="if(maxStepNumber >= 5) step=5">
                        <div class="d-grid text-center align-items-center">
                            <button type="button"
                                    class="btn btn-link step-trigger mb-0">
                                <span class="bs-stepper-circle">5</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bs-stepper-content px-0">
                <div x-show="step == 1" x-transition>

                    <div>
                        <form
                            action="{{route('front.post-public-account-form', ['locale' => app()->getLocale(), 'token' => $instance->id]) }}"
                            method="post"
                            enctype="multipart/form-data"
                            class="fs-14 account-container">
                            @csrf

                            <input type="hidden"
                                   name="event_group_id"
                                   value="{{ $event_group_id }}"/>

                            <x-front.form-errors/>

                            <livewire:front.user.info-section :account="$account"
                                                              :event="$event"
                                                              :registrationType="$registrationType"/>

                            <div class="d-flex justify-content-center mt-3">
                                <button
                                    type="submit"
                                    class="btn btn-primary next-btn mb-0">
                                    {{__('front/register.next') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div x-show="step == 2" x-transition>
                    @if($account)
                        <div>

                            <livewire:front.user.mail-section :account="$account"
                                                              :show-submit-button="false"/>
                        </div>
                        <div>
                            <livewire:front.user.phone-section :account="$account"
                                                               :show-submit-button="false"/>
                        </div>
                        <div>
                            <livewire:front.user.address-section :account="$account"/>
                        </div>
                    @else
                        @include ('front.auth.register-form.complete-step-1')
                    @endif

                    <div class="d-flex justify-content-center mt-3 gap-2">
                        <x-register-link-previous
                            :token="$instance->id"
                            :event_group_id="$event_group_id"
                            step="1"/>
                        <button
                            @click.prevent="submitCoordinates()"
                            class="btn btn-primary next-btn mb-0">
                            {{__('front/register.next') }}
                        </button>
                    </div>
                </div>
                <div x-show="step == 3" x-transition>

                    @if($account)
                        <div>
                            <livewire:front.user.identity-card-section :account="$account"/>
                        </div>
                        <div>
                            <livewire:front.user.loyalty-card-section :account="$account"/>
                        </div>
                        <div class="mt-3">
                            <x-mediaclass::uploadable :model="$account"
                                                      group="transport_user_docs"
                                                      size="small"
                                                      icon="bi bi-file-earmark-arrow-up-fill"
                                                      :description="false"
                                                      :nomedia="__('mediaclass.no_documents')"
                                                      :label="__('front/ui.media.add_traveil_documents')"/>
                        </div>
                    @else
                        @include ('front.auth.register-form.complete-step-1')
                    @endif

                    <div class="d-flex justify-content-center mt-3 gap-2">
                        <x-register-link-previous
                            :token="$instance->id"
                            :event_group_id="$event_group_id"
                            step="2"/>

                        <a href="{{  route('front.register-public-account-form', [
                                    'locale' => app()->getLocale(),
                                    'token' => $instance->id,
                                    'event_group_id' => $event_group_id,
                                    'step' => 4,
                                ]) }}"
                           class="btn btn-dark next-btn mb-0">
                            {{ __('front/register.next') }}
                        </a>
                    </div>

                </div>

                <div x-show="step == 4" x-transition>


                    <form action="{{route('front.registerCredentials', ['token' => $instance->id]) }}"
                          method="post"
                          enctype="multipart/form-data"
                          class="fs-14 account-container">
                        @csrf
                        @method('PUT')

                        <x-front.form-errors/>

                        <input type="hidden"
                               name="event_group_id"
                               value="{{ $event_group_id }}"/>

                        @include ('front.user.credentials.credentials-card')

                        <div class="d-flex justify-content-center mt-3 gap-2">
                            <x-register-link-previous :token="$instance->id"
                                                      :event_group_id="$event_group_id"
                                                      step="3"/>
                            <button type="submit"
                                    class="btn btn-primary next-btn mb-0">
                                {{__('front/register.next') }}
                            </button>
                        </div>
                    </form>

                </div>
                <div x-show="step == 5" x-transition>
                    <div class="alert alert-success">
                        {{__('account.account_created', ['names' => $account->names() ]) }}
                    </div>
                    <div class="d-flex justify-content-center mt-3 gap-2">
                        <x-register-link-previous :token="$instance->id"
                                                  :event_group_id="$event_group_id"
                                                  step="4"/>
                        @php
                            $route = $event_group_id
                            ? route('front.event.associate-group-member-and-back', $event)
                            : route('front.event.registerLogin', $event);
                        @endphp

                        <form action="{{ $route }}"
                              method="post"
                              enctype="multipart/form-data"
                              class="fs-14 account-container">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="email" value="{{ $account?->email }}"/>
                            <input type="hidden"
                                   name="event_group_id"
                                   value="{{ $event_group_id }}"/>


                            @if($event_group_id)
                                <button type="submit"
                                        class="btn btn-primary next-btn mb-0">
                                    {{ __('front/groups.back_to_members') }}
                                </button>
                            @else
                                <button
                                    type="submit"
                                    class="btn btn-primary next-btn mb-0">
                                    {{__('front/register.go_to_event') }}
                                </button>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('front.shared.confirm_modal')


    @pushonce("common_scripts")
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
                crossorigin="anonymous"></script>
    @endpushonce

    @pushonce('css')
        <link rel="stylesheet" href="{{ asset('vendor/bs-stepper/bs-stepper.min.css')  }}">
    @endpushonce


    @push("js")

        <script>
            let alreadyValidated = false;

            function submitCoordinates() {

                let submissionState = {
                    mainEmailValidated: false,
                    atLeastOneAddress: false,
                };

                Livewire.on('mainEmailSaved', () => {
                    submissionState.mainEmailValidated = true;
                    checkAndSubmit();
                });

                Livewire.on('AddressSection.atLeastOneAddress', () => {
                    submissionState.atLeastOneAddress = true;
                    checkAndSubmit();
                });

                Livewire.dispatch('validateMainEmail');
                Livewire.dispatch('AddressSection.checkAtLeastOneAddress');

                function checkAndSubmit() {
                    if (
                        submissionState.mainEmailValidated &&
                        submissionState.atLeastOneAddress
                    ) {
                        if (!alreadyValidated) {
                            alreadyValidated = true;
                            window.location.href = {{  Js::from(route('front.register-public-account-form', [
                        'locale' => app()->getLocale(),
                        'token' => $instance->id,
                        'event_group_id' => $event_group_id,
                        'step' => 3,
                    ])) }};
                        }
                    }
                }
            }

        </script>
    @endpush

</x-front-layout>
