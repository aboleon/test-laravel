@php
    $departureDate = \App\Accessors\Chronos::formatDate($transport?->departure_start_date) ?? $event->starts;
@endphp
<div class="card mb-3 tr-base">
    <div class="card-body">
        <div class="row g-5">
            <div class="col-md-6">
                <h4 class="mt-3 mb-5">{{__('transport.departure_transport')}}</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-mfw::datepicker name="item[main][departure_start_date]"
                                           label="{{__('transport.labels.departure_start_date')}}"
                                           value=""
                                           config="dateFormat={{config('app.date_display_format')}},defaultDate={{$error ? old('item.main.departure_start_date') : $departureDate}}"/>
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-back.time-mask-mfw
                            name="item[main][departure_start_time]"
                            label="{{__('transport.labels.departure_start_time')}}"
                            :params="['placeholder' => \App\Accessors\Dates::getFrontHourMinuteFormat('placeholder')]"
                            value="{{old('item.main.departure_start_time', $transport?->departure_start_time?->format('H:i'))}}"
                            x-mask="{{\App\Accessors\Dates::getFrontHourMinuteFormat('x-mask')}}"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-back.time-mask-mfw
                            name="item[main][departure_end_time]"
                            label="{{__('transport.labels.departure_end_time')}}"
                            :params="['placeholder' => \App\Accessors\Dates::getFrontHourMinuteFormat('placeholder')]"
                            value="{{old('item.main.departure_end_time', $transport?->departure_end_time?->format('H:i'))}}"
                            x-mask="{{\App\Accessors\Dates::getFrontHourMinuteFormat('x-mask')}}"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-mfw::input name="item[main][departure_start_location]"
                                      :value="$error ? old('item.main.departure_start_location') : $transport?->departure_start_location"
                                      :label="__('transport.departure_start_location')"/>
                    </div>
                    <div class="col-md-6">
                        <x-mfw::input name="item[main][departure_end_location]"
                                      :value="$error ? old('item.main.departure_end_location') : $transport?->departure_end_location"
                                      :label="__('transport.departure_end_location')"/>
                    </div>
                    <div class="col-md-6 mfw-holder position-relative mb-3">
                        <div class="d-flex justify-content-between align-items-end">
                            <div class="w-100 me-3">
                                <x-selectable-dictionnary key="transport"
                                                          name="item[main][departure_transport_type]"
                                                          :affected="$error ? old('item.main.departure_transport_type') : $transport?->departure_transport_type"/>
                            </div>
                            <span class="fs-4 add-dynamic dict-dynamic"
                                  data-dict="transport"><i
                                    class="fa-solid fa-circle-plus"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 mfw-holder position-relative mb-3">
                        <div class="d-flex justify-content-between align-items-end">
                            <div class="w-100 me-3">
                                <x-selectable-dictionnary key="transport_step"
                                                          name="item[main][departure_step]"
                                                          :affected="$error ? old('item.main.departure_step') : $transport?->departure_step"/>
                                <div class="form-text">
                                    Infos affichées en front si le statut
                                    contient "ok"
                                </div>
                            </div>
                            <span class="fs-4 add-dynamic dict-dynamic"
                                  data-dict="transport_step"><i
                                    class="fa-solid fa-circle-plus"></i></span>
                        </div>
                    </div>

                    <div class="col-12 mb-3 tr-divine">
                        <x-mfw::textarea label="{{__('transport.reference_info_participant')}}"
                                         height="100"
                                         name="item[main][departure_reference_info_participant]"
                                         :value="$error ? old('item.main.departure_reference_info_participant') : $transport?->departure_reference_info_participant"/>

                    </div>
                    <div class="col-12 tr-base">
                        <x-mfw::textarea label="{{__('transport.participant_departure_comment')}}"
                                         height="100"
                                         name="item[main][departure_participant_comment]"
                                         :value="$error ? old('item.main.departure_participant_comment') : $transport?->departure_participant_comment"/>
                    </div>
                    <div class="col-12 tr-divine mt-3">
                        <x-mfw::checkbox :switch="false"
                                         name="item[main][departure_online]"
                                         value="1"
                                         label="{{__('transport.booking_finalized_for_this_step')}}"
                                         :affected="collect($error ? old('item.main.departure_online') : ($transport?->id ? $transport?->departure_online : [0]))"/>

                        {{__('transport.participant_ticket_submission_notification')}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="mt-3 mb-5">Transfert aller</h4>
                @if($transport && !$transport->transfer_requested)
                    <div class="row mb-3 tr-base">
                        <div class="col-md-6 mb-3">
                            <h6 class="d-flex align-items-center">
                                Transfert non {{ is_null($transport->transfer_requested) ? 'renseigné' : 'souhaité' }}
                                <i class="fa-solid fa-{{ is_null($transport->transfer_requested) ? 'circle-exclamation' : 'circle-xmark icon-failure' }} fs-4 ms-2"></i>
                            </h6>
                        </div>
                    </div>
                @else($transport?->transfer_requested)
                    <!-- transfert souhaité -->
                    <div class="row mb-3 tr-base">
                        <div class="col-md-6 mb-3">
                            <h6 class="d-flex align-items-center">
                                {{__('transport.transfer_requested')}}
                                <i class="fa-regular fa-circle-check fs-4 ms-2 icon-success"></i>
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <x-back.time-mask-mfw
                                name="item[main][transfer_shuttle_time_departure]"
                                label="{{__('transport.shuttle_time')}}"
                                :params="['placeholder' => \App\Accessors\Dates::getFrontHourMinuteFormat('placeholder')]"
                                value="{{old('item.main.transfer_shuttle_time_departure', $transport?->transfer_shuttle_time_departure?->format('H:i'))}}"
                                x-mask="{{\App\Accessors\Dates::getFrontHourMinuteFormat('x-mask')}}"
                            />

                        </div>
                    </div>
                    <div class="row mb-3">
                        <x-mfw::textarea label="{{__('transport.transfer_info')}}"
                                         height="100"
                                         name="item[main][transfer_info_departure]"
                                         :value="$error ? old('item.main.transfer_info_departure') : $transport?->transfer_info_departure"/>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
