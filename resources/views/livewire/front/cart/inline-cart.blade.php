<div>
    @if(!$status && !$count)
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
        @php
            $serviceLines = $this->cartLines['services'];
            $stayLines = $this->cartLines['stays'];
            $grantWaiverFeesLines = $this->cartLines['grantWaiverFees'];
        @endphp
        <div wire:key="{{rand()}}"
             class="container" x-data="{page: '{{$page}}', paymentMethod: 'cb'}">

            <h3 class="mb-4 p-2 divine-main-color-text rounded-1">{{__('My Cart - reservations')}}</h3>

            <div x-cloak x-show="page==='pay_result'" class="row g-4 g-sm-5">
                @include('front.cart.inline-cart.pay_result')
            </div>


            <div class="row g-4 g-sm-5" x-show="'pay_result' !== page" x-cloak>
                <div x-show="page==='cart'" class="col-lg-8 mb-4 mb-sm-0">
                    <div class="card card-body border p-4 shadow">


                        @if($serviceLines->isNotEmpty())
                            @include('front.cart.inline-cart.services.service-lines')
                        @endif
                        @if($stayLines->isNotEmpty())
                            @include('front.cart.inline-cart.accommodation.accommodations-lines')
                        @endif
                        @if($grantWaiverFeesLines->isNotEmpty())
                            @include('front.cart.inline-cart.grant.waiver-fees-lines')
                        @endif
                    </div>
                </div>

                <div x-show="page==='payment'" class="col-lg-8 mb-4 mb-sm-0">
                    @include('front.cart.inline-cart.payment')
                </div>
                <div class="col-lg-4">
                    @php
                        $cartLines = $this->cartLines;
                    @endphp
                    @include('front.cart.inline-cart.total-float-block')
                </div>

            </div>
        </div>


        <x-use-lightbox/>

        @include('front.cart.inline-cart.reconfirm-preorder-failed-modal')

        @push('css')
            <style>
                .popup-cart-btn-container {
                    display: none !important;
                }
            </style>
        @endpush

        @push("js")
            <script>
                $(document).ready(function () {
                    Livewire.on('PopupCart.updateServiceQuantityAfter', function (res) {
                        if (res.error) {
                            AjaxNotifModal.messagePrinter(200, res.ajax_messages);
                        }
                    });
                    /*
                    TODO: voir si ça impacte pour de vrai, modal pour reload
                    Livewire.on('PopupCart.confirmRecheckPreorderFailed', function () {
                        $('#reconfirmPreorderFailedModal').modal('show');
                    });
                    */
                    Livewire.on('PopupCart.confirmRecheckPreorderSuccess', function () {
                        $('#inline-cart-payment-button').click();
                    });
                });
            </script>
        @endpush
    @endif
</div>
