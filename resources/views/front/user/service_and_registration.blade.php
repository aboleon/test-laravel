@php
    use App\Accessors\EventManager\Sellable\Deposits;use App\Accessors\EventManager\SellableAccessor;use App\Helpers\Front\PriceHelper;
@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs/>

    <h3 class="mb-4 p-2 divine-main-color-text rounded-1">{{__('front/services.services_and_registrations')}}</h3>

    @php
        $eventContactAccessor = (new \App\Accessors\EventContactAccessor())->setEventContact($eventContact);
    @endphp


    <div class="accordion accordion-icon accordion-bg-light accordion-bg-light-with-border"
         id="services-context" data-ajax="{{route('ajax')}}">
        <div class="messages"></div>

        @forelse($allowedGroupedServices as $groupName => $services)
            @php
                $i = $loop->index;
            @endphp
            <div class="accordion-item mb-3">
                <h6 class="accordion-header" id="heading{{$i}}">
                    <button class="accordion-button rounded collapsed fs-4 d-block"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{$i}}"
                            aria-expanded="false"
                            aria-controls="collapse{{$i}}">
                        <span class="fw-bold divine-secondary-color-text">{{$groupName}}</span>
                    </button>
                </h6>
                <div id="collapse{{$i}}"
                     class="accordion-collapse collapse show"
                     aria-labelledby="heading{{$i}}"
                     data-bs-parent="#services-context"
                     style="">
                    <div class="accordion-body mt-3">
                        <div class="card-body">

                            @foreach($services as $k => $service)
                                @if($k > 0)
                                    <hr>
                                @endif
                                <div class="d-sm-flex sellable-service-container"
                                     data-id="{{$service->id}}">
                                    <div class="w-100">
                                        <div class="mb-3 d-sm-flex justify-content-sm-between align-items-center">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6">
                                                        <h5 class="m-0 mb-2 d-block align-items-center">
                                                            <div>
                                                                {{$service->title}}
                                                                <x-front.debugmark title="{{$service->id}}"/>
                                                            </div>
                                                        </h5>
                                                        <span class="me-3 small d-flex gap-3">
                                                            @if($service->service_date)
                                                                <span><i class="bi bi-calendar-date"></i> {{$service->service_date}}</span>
                                                            @endif
                                                            @if($service->service_starts)
                                                                <span>
                                                                    <i class="bi bi-alarm"></i> {{$service->service_starts->format('H\hi')}}
                                                                    @if($service->service_ends)
                                                                        - {{$service->service_ends->format('H\hi')}}
                                                                    @endif
                                                                </span>
                                                            @endif

                                                            @if($service->place_id)
                                                                <span>
                                                        <i class="bi bi-signpost"></i> {{$service->place->name}}
                                                                    @if($service->room_id)
                                                                        >
                                                                        <span>
                                                                            {{$service->room->name}}
                                                                            @if($service->room->level > 0)
                                                                                ({{__('front/services.room_level')}} {{$service->room->level}}
                                                                                )
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                        </span>
                                                            @endif

                                                </span>
                                                    </div>


                                                    <div class="col-12 col-lg-6 fw-bold">
                                                        @if(!$service->stock_unlimited)
                                                            @php
                                                                $nbRemainingStock = null;
                                                                if($service->stock_showable){
                                                                    if($service->stock && $service->stock <= $service->stock_showable){
                                                                        $nbRemainingStock = $service->stock;
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="stock-showable-container text-info fs-6 mt-2"
                                                                 data-stock-showable="{{$service->stock_showable}}"
                                                                 style="display: {{($nbRemainingStock) ? 'block' : 'none'}}"
                                                            >
                                                                <i class="bi bi-info-circle"></i>
                                                                {!! __('front/services.only_x_places_remaining', ['nb_places' => $nbRemainingStock]) !!}
                                                            </div>
                                                        @endif

                                                        @if($service->options->isNotEmpty())
                                                            <div class="text-success-emphasis fs-6 mt-2">
                                                                <i class="bi bi-check2-square"></i>
                                                                @foreach($service->options as $option)
                                                                    {{$option->description}}
                                                                    <br>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if($service->service_group_combined)
                                                            <div class="text-danger fs-6 mt-2">
                                                                <i class="bi bi-exclamation-circle"></i>&nbsp;
                                                                {{__('front/services.only_available_with_service_of_type', ['type' => $service->groupCombined->name])}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row align-items-end">
                                            <div class="col-12 col-md-6">
                                                <p>{{$service->description}}</p>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <table
                                                    class="table table-sm table-responsive table-dark-gray align-middle p-4 mb-0 table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col"
                                                            class="border-0">{{__('front/services.col_quantity')}}
                                                        </th>
                                                        <th scope="col"
                                                            class="border-0">{{__('front/services.col_price')}}
                                                        </th>
                                                        <th scope="col"
                                                            class="border-0 rounded-end">
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr data-id="{{$service->id}}">
                                                        <td>
                                                            <select class="select-quantity">
                                                                @for($i = 1; $i <= 10; $i++)
                                                                    <option value="{{$i}}">{{$i}}</option>
                                                                @endfor
                                                            </select>

                                                        </td>
                                                        <td>
                                                            @php
                                                                $price = SellableAccessor::getRelevantPrice($service);
                                                            @endphp
                                                            @if($eventContactAccessor->isPecAuthorized() && $service->pec_eligible && $price)
                                                                <span class="text-decoration-line-through">
                                                                    {{PriceHelper::frontPriceWithDecimal($price)}}
                                                                </span>
                                                                @if(!$service->deposit)
                                                                    <br>
                                                                    0 €
                                                                @endif
                                                                <br>
                                                                <div class="badge badge rounded-pill text-bg-success">
                                                                    {{ __('front/order.pec') }} {!! (int)$service->pec_max_pax ? '&nbsp; / max '.$service->pec_max_pax :'' !!}
                                                                </div>
                                                                @if($service->deposit)
                                                                    <br>
                                                                    <span
                                                                        class=""><b>{{PriceHelper::frontPriceWithDecimal(Deposits::getSellableDepositAmount($service->deposit))}}</b> {{ __('front/order.of_deposit') }}</span>
                                                                @endif
                                                            @else
                                                                {{PriceHelper::frontPriceWithDecimal($price)}}
                                                                @if($service->deposit)
                                                                    <br>
                                                                    <span
                                                                        class="smaller">Dont <b>{{PriceHelper::frontPriceWithDecimal(Deposits::getSellableDepositAmount($service->deposit))}}</b> {{ __('front/order.of_deposit') }}</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button
                                                                class="btn btn-sm btn-primary action-add-to-cart d-flex align-items-center gap-2">

                                                                {{__('front/services.book')}}
                                                                <div
                                                                    class="spinner-service-item spinner-border spinner-border-sm"
                                                                    style="display: none;"
                                                                    role="status">
                                                                    <span
                                                                        class="visually-hidden">{{ __('front/ui.loading') }}</span>
                                                                </div>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>{{__('front/services.no_services_for_now')}}</p>
        @endforelse
    </div>

    @push("modals")
        @include("front.shared.modal.simple_confirm_modal")
    @endpush

    @push("js")
        <script src="{{ asset('front/js/service_registration.js') }}"></script>
    @endpush

</x-front-logged-in-layout>
