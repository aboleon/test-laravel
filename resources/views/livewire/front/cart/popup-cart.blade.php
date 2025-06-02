@php
    use App\Accessors\EventManager\SellableAccessor;
    use App\Accessors\Front\FrontCache;
    use App\Accessors\Order\Cart\ServiceCarts;
    use App\Helpers\Price\DivinePrice;
    use Carbon\Carbon;
@endphp
<div class="dropdown popup-cart-btn-container">
    <a class="dropdown-toggle d-flex gap-2 align-items-center position-relative"
       id="popup-cart-button"
       href="#"
       role="button"
       data-bs-toggle="dropdown"
       data-bs-auto-close="false"
       aria-expanded="false">
        <i class="fa-solid fa-cart-shopping"></i>
        @if($count)
            <span
                class="badge bg-dribbble smaller position-absolute top-50 start-100 translate-middle rounded-pill">{{$count}}</span>
        @endif
    </a>

    <div class="popup-cart dropdown-menu dropdown-menu-end {{$showCart?'show':''}}">
        @if($count)
            <div x-data="cartTimer(new Date('{{$expirationTime}}'))"
                 class="small text-end"
                 wire:key="{{rand()}}"
            >
                <div class="p-1 text-bg-blue"
                     x-show="time().hours >= 1 || time().minutes >= 1 || time().seconds >= 1">
                    <span class="smaller">{{__('front/cart.popup_cart_expires_in')}}</span>
                    <span x-text="time().minutes"></span> {{__('front/cart.popup_cart_minutes')}}
                    <span x-text="time().seconds"></span> {{__('front/cart.popup_cart_seconds')}}
                </div>
                <div class="p-1 text-bg-danger"
                     x-show="time().hours < 1 && time().minutes < 1 && time().seconds < 1">
                    <span class="smaller">{{__('front/cart.popup_cart_expired')}}</span>
                </div>

            </div>
            <h4 class="text-end d-flex align-items-center mt-2">
                    <span class="fs-14">
                        {{__('front/cart.popup_cart_subtotal')}}
                        <x-front.livewire-ajax-spinner class="border-start"
                                                       target="updateServiceQuantity"/>
                    </span>
                @if($isPecEligible && $itemsTotalPec)
                    <span class="text-success-emphasis ms-auto text-decoration-line-through smaller">{{$itemsTotalTtc}} €</span>
                    <span class="text-success-emphasis ms-2">{{$itemsTotalTtc - $itemsTotalPec}} €</span>
                @else
                    <span
                        class="ms-auto {{ $amendableAmount ? 'text-decoration-line-through text-secondary' : 'text-success-emphasis' }}">{{ $itemsTotalTtc + $amendableAmount }} €</span>
                    @if ($amendableAmount)
                        <span class="ps-2 text-success-emphasis">{{ $itemsTotalTtc}} €</span>
                    @endif
                @endif
            </h4>
            @if($amendableAmount)
                <div id="cart-amendable-amount" class="text-end mb-2" style="margin-top:-5px">
                    {{__('front/cart.popup_cart_already_paid') . $amendableAmount }} €
                </div>
            @endif
            <div class="text-end">
                @php
                    $cartRoute = null;
                    if(FrontCache::isConnectedAsGroupManager()){
                        $cartRoute = route('front.event.switch-back-and-go-to-group-buy', [
                            'locale' => app()->getLocale(),
                            'event' => $this->eventContact->event_id,
                        ]);
                    }
                    else{
                        if($this->isGroupManager){
                            $cartRoute = route('front.event.group.checkout', [
                                "locale" => app()->getLocale(),
                                "event" => $eventContact->event_id,
                            ]);
                        }
                        else{
                            $cartRoute = route('front.event.cart.edit', [
                                "locale" => app()->getLocale(),
                                "event" => $eventContact->event_id,
                            ]);
                        }
                    }
                @endphp
                <a
                    x-data="{clicked: false}"
                    href="{{$cartRoute}}"
                    @click="clicked=true"
                    class="btn btn-small btn-success btn-link"
                    data-route="{{ $cartRoute }}"
                    id="gotocart">
                    @if(FrontCache::isConnectedAsGroupManager())
                        Aller au panier de groupe
                        <div x-cloak
                             x-show="clicked"
                             class="spinner-border spinner-border-sm"
                             role="status">
                            <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                        </div>

                    @else
                        {{__('front/cart.popup_cart_go_to_cart')}}
                    @endif

                </a>
            </div>
            @if(
                count($this->cartLines['services']) > 0 ||
                count($this->cartLines['stays']) > 0 ||
                count($this->cartLines['grantWaiverFees']) > 0
            )
                @php
                    $serviceLines = $this->cartLines['services'];
                    $stayLines = $this->cartLines['stays'];
                    $grantWaiverFeeLines = $this->cartLines['grantWaiverFees'];
                @endphp
                <table class="popup-cart-table table table-sm mt-2">
                    <thead>
                    <tr>
                        <th class="col1">{{__('front/cart.popup_cart_col_service')}}</th>
                        <th class="d-none d-sm-block">{{__('front/cart.popup_cart_col_quantity')}}</th>
                        <th>{{__('front/cart.popup_cart_col_subtotal')}}</th>
                        <th>{{__('front/cart.popup_cart_col_actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>


                    @include('livewire.front.cart.popup-cart.popup-cart-service-lines')
                    @include('livewire.front.cart.popup-cart.popup-cart-stay-lines')
                    @include('livewire.front.cart.popup-cart.popup-cart-grant-waiver-fee-lines')


                    </tbody>
                </table>
            @endif
        @else
            <p class="text-center mt-3 p-4 rounded-4 text-bg-light">
                {{__('front/cart.popup_cart_is_empty')}}
            </p>
        @endif
    </div>
</div>


