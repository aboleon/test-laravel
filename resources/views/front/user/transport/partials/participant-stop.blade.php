<div x-show="'recap' === page" x-cloak class="container">
    <h3 class="mb-4 p-2 bg-primary-subtle rounded-1">{{__('Mon transport')}}</h3>


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
                            {{__('front/transport.i_manage_my_transport')}}
                        </label>
                    </div>

                    <p class="mt-3 small">
                        {{$event->texts->transport_user}}
                    </p>

                </div>
            </div>
        </div>
    </div>

    <x-mfw::alert type="primary" class="fs-5" :message="__('front/transport.elements_submitted_we_will_contact_you_soon')"/>

    <div>
        <x-mediaclass::uploadable :model="$account"
                                  group="transport_user_docs"
                                  size="small"
                                  icon="bi bi-file-earmark-arrow-up-fill"
                                  :description="false"
                                  :nomedia="__('mediaclass.no_documents')"
                                  :label="__('front/ui.media.add_travel_documents')"/>


    @include('front.user.transport.partials.info-transfer', ['transfer_type'=>'_departure'])
    @include('front.user.transport.partials.info-transfer', ['transfer_type'=>'_return'])

</div>
