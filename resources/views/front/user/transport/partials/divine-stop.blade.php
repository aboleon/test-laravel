@php
    use App\Accessors\EventManager\TransportAccessor;
    use App\Accessors\Users;

    $departureStepOk = TransportAccessor::transportDepartureStepIsOk($transport);
    $returnStepOk = TransportAccessor::transportReturnStepIsOk($transport);

@endphp

<div x-show="'recap' === page" x-cloak class="container">
    <h3 class="mb-4 p-2 bg-primary-subtle rounded-1">{{__('front/transport.my_transport')}}</h3>

    @if(!$departureStepOk && !$returnStepOk)
        <p>{{__('front/transport.you_have_previously_indicated')}}</p>


        <div class="row">
            <div class="mb-3 col-12 col-md-6">
                <div class="card">
                    <div class="card-body border">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   value="participant"
                                   name="flexRadioDefault"
                                   id="transport_mode1"
                                   checked
                            >
                            <label class="form-check-label fw-bold text-dark"
                                   for="transport_mode1">
                                {{__('front/transport.organization_manages_transport')}}
                            </label>
                        </div>

                        <p class="mt-3 small">
                            {{ $event->texts->transport_user }}
                        </p>

                    </div>
                </div>
            </div>
        </div>
        <x-mfw::alert type="primary" class="fs-5"
                      :message="__('front/transport.elements_submitted_we_will_contact_you_soon')"/>


        @include('front.user.transport.partials.' . $transport->desired_management.'-step-recap', ['disable_submit_transport_form' => true])

    @else
        <x-mfw::alert type="primary" class="fs-5"
                      :message="__('front/transport.as_discussed_before_your_travel_info')"/>
        <h5>{{__('front/transport.departure')}}</h5>
        @if( $departureStepOk )
            <x-eventManager.transport.transport-departure-info :transport="$transport"/>
            @include('front.user.transport.partials.info-transfer', ['transfer_type'=>'_departure'])
        @else
            <p>
                {{__('front/transport.in_process')}}
            </p>
        @endif
        <h5>{{__('front/transport.return')}}</h5>
        @if ( $returnStepOk )
            <x-eventManager.transport.transport-return-info :transport="$transport"/>
            @include('front.user.transport.partials.info-transfer', ['transfer_type'=>'_return'])
        @else
            <p>{{__('front/transport.in_process')}}</p>
        @endif

        <x-mediaclass::uploadable :model="$account"
                                  group="transport_docs"
                                  size="small"
                                  icon="bi bi-file-earmark-arrow-up-fill"
                                  :description="false"
                                  :nomedia="__('mediaclass.no_documents')"
                                  :label="__('front/ui.media.add_travel_documents')"/>
    @endif
</div>
