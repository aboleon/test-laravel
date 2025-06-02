@php
    use App\Accessors\Order\Cart\ServiceCarts;
    use App\Helpers\Front\DivinePriceHelper;
@endphp
@if($serviceLines->count() > 0)

    <div class="row">
        <div class="divine-secondary-color-text d-flex align-items-center fade show py-3 pe-2"
             role="alert">
            <i class="bi bi-person-lines-fill fa-fw me-1"></i>
            {{__('front/cart.services')}}
        </div>
    </div>
    <div class="d-block">
        @foreach($serviceLines as $serviceLine)
            @php
                $dPrice = DivinePriceHelper::getDivinePrice($serviceLine);
                $quantity = $serviceLine['quantity'];
                $service = $serviceLine->shoppable;
                $cart = $serviceLine->cart;
            @endphp
            <div class="card row mb-2"
                 wire:key="{{rand()}}"
                 x-data="{
                    quantity: {{$quantity}},
                    showQuantity: false,
                    showDelete: false,
                }"
            >
                <div class="card-body border">
                    <div class="row border-bottom border-top border-light-subtle">
                        <div class="col-4 text-bg-light text-body">{{__('front/cart.services_label')}}</div>
                        <div class="col-8 text-dark">
                            {{$service->title}}
                            <x-front.debugmark title="{{$service->id}}" />
                        </div>
                    </div>
                    @if($service->service_date)
                        <div class="row border-bottom border-light-subtle">
                            <div class="col-4 text-bg-light text-body">{{__('front/cart.services_dates')}}</div>
                            <div class="col-8 text-dark">
                                {{$service->service_date}}
                                @if ($service->service_starts)
                                    {{__('front/cart.services_date_at')}} {{$service->service_starts->format("H\hi")}}
                                @endif
                            </div>
                        </div>
                    @endif
                    @php
                        $serviceTtc = $dPrice->ttc();
                    @endphp
                    <div class="row border-bottom border-light-subtle">
                        <div class="col-4 text-bg-light text-body">{{__('front/cart.services_price')}}</div>
                        <div class="col-8 text-dark">
                            @if($cart->pec_eligible && $serviceLine->total_pec)
                                <span class="text-decoration-line-through smaller">{{$serviceTtc}} €</span>
                                {{$serviceTtc - $serviceLine->total_pec}} €
                            @else
                                {{$serviceTtc}} €
                            @endif
                        </div>
                    </div>
                    <div class="row border-bottom border-light-subtle">
                        <div class="col-4 text-bg-light text-body pt-1">{{__('front/cart.services_quantity')}}
                        </div>
                        <div class="col-8 text-dark d-flex gap-3 align-items-center">
                            {{$quantity}}
                        </div>
                        <div class="d-none col-8 text-dark d-flex gap-3 align-items-center">
                            <input type="number"
                                   value="1"
                                   min="1"
                                   x-model="quantity"
                                   @change="showQuantity=true"
                                   class="form-control form-control-sm w-auto">
                            <div x-show="showQuantity" x-cloak
                                 class="d-flex gap-2 align-items-center"
                            >
                                <button :style="{
                                            visibility: showQuantity ? 'visible' : 'hidden'
                                        }"
                                        x-cloak
                                        wire:click.prevent="updateServiceQuantity({{$service->id}}, quantity);"
                                        class="btn btn-link mt-1 ps-0">
                                    <i title="Valider" class="bi bi-arrow-clockwise"></i>
                                    <x-front.livewire-ajax-spinner class="border-start"
                                                                   target="updateServiceQuantity" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2 p-2 align-items-center">
                        <button class="btn btn-danger btn-sm d-flex gap-0 align-items-center"
                                x-on:click.prevent="showDelete=true;$wire.removeService({{$service->id}}, {{$serviceLine->front_cart_id}});"
                                href="#">
                            {{__('front/cart.services_delete')}}
                            <div x-show="showDelete" x-cloak>
                                <x-front.livewire-ajax-spinner class="border-start"
                                                               target="removeService" />
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
