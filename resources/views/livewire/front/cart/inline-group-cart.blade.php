@php

    use App\Enum\OrderType;use App\Helpers\Front\Cart\FrontGroupCart;use App\Models\EventManager\Sellable;use App\Models\FrontCart;

    if (\App\Accessors\Front\FrontCache::isConnectedAsGroupManager() or !isset($isPecEligible)) {
    $isPecEligible =  false;
    }
    $groupCart = FrontGroupCart::getInstance($eventContact);
    $carts = $groupCart->getCarts(true);
    $carts = $carts->filter(function(FrontCart $cart){
        return $cart->lines()->count() > 0;
    });
    $expirationTime = $groupCart->getExpirationTime();
@endphp
<div>

    <div x-data="cartTimer(new Date('{{$expirationTime}}'))"
         class="small text-end"
         wire:key="{{rand()}}"
    >
        <div class="p-1 text-bg-blue mx-3 mb-3 rounded"
             x-cloak
             x-show="time().hours >= 1 || time().minutes >= 1 || time().seconds >= 1">
            <span class="smaller">{{__('front/cart.popup_cart_expires_in')}}</span>
            <span x-text="time().minutes"></span> {{__('front/cart.popup_cart_minutes')}}
            <span x-text="time().seconds"></span> {{__('front/cart.popup_cart_seconds')}}
        </div>
        <div class="p-1 text-bg-danger"
             x-cloak
             x-show="time().hours < 1 && time().minutes < 1 && time().seconds < 1">
            <span class="smaller">{{__('front/cart.popup_cart_expired')}}</span>
        </div>

    </div>

    @if($groupCart->isEmpty())
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <p class="text-center mt-3">
                            {{__('front/cart.inline_cart_is_empty')}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container"
             wire:key="{{rand()}}"
             x-data="{page: '{{$this->page}}', paymentMethod: 'cb'}"
        >

            <h3 class="mb-4 p-2 divine-main-color-text rounded-1">Mon panier de groupe</h3>

            <div x-cloak x-show="'pay_result' === page">
                @include('front.cart.inline-cart.pay_result')
            </div>

            <div class="row g-4 g-sm-5" x-show="'pay_result' !== page" x-cloak>


                <div x-cloak x-show="'cart' === page"
                     class="col-lg-8 mb-4 mb-sm-0"
                >
                    <div class="accordion"
                         id="accordionOrder">
                        @foreach($carts as $cart)
                            @php
                                $collapseClassBtn = 'collapsed';
                                $collapseClassItem = '';
                                $ariaExpanded = 'false';
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{$collapseClassBtn}}"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{$cart->id}}"
                                            aria-expanded="{{$ariaExpanded}}"
                                            aria-controls="collapse-{{$cart->id}}">
                                        {{$cart->eventContact->user->names()}}
                                    </button>
                                </h2>
                                <div id="collapse-{{$cart->id}}"
                                     class="accordion-collapse collapse {{$collapseClassItem}}"
                                     data-bs-parent="#accordionOrder">
                                    <div class="accordion-body">
                                        @php
                                            $serviceLines = $cart->lines()->where('shoppable_type', Sellable::class)->get();
                                            $stayLines = $cart->lines()->where('shoppable_type', "stay")->get();
                                            $grantWaiverFeesLines = $cart->lines()->where('shoppable_type', OrderType::GRANTDEPOSIT->value)->get();
                                        @endphp
                                        @include('front.cart.inline-cart.services.service-lines')
                                        @include('front.cart.inline-cart.accommodation.accommodations-lines')
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="page==='payment'" class="col-lg-8 mb-4 mb-sm-0">
                    @include('front.cart.inline-cart.payment')
                </div>

                <div class="col-4">
                    @php
                        $serviceLines = $groupCart->getServiceLines();
                        $stayLines = $groupCart->getStayLines();
                        $cartLines = [
                            'services' => $serviceLines,
                            'stays' => $stayLines,
                        ];
                    @endphp
                    @include('front.cart.inline-cart.total-float-block')
                </div>
            </div>


        </div>
    @endif


    @push("js")
        <script>
            $(document).ready(function () {
                Livewire.on('InlineGroupCart.confirmRecheckPreorderSuccess', function () {
                    $('#inline-cart-payment-button').click();
                });
            });
        </script>
    @endpush
</div>
