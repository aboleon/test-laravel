@php
    $accountAccessor = (new \App\Accessors\Accounts($account));
@endphp
<x-front-logged-in-layout :event="$event">
    <x-front.session-notifs/>

    @if(!$accountAccessor->hasValidBillingAddress())
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        {!! __('front/account.has_no_valid_address', ['url' => route("front.event.coordinates.edit", ['event' => $event])]) !!}
                    </div>
                </div>
            </div>
        </div>
    @else
        <livewire:front.cart.inline-cart :cart="$cart" :eventContact="$eventContact->setRelations([])" :status="$status"/>
    @endif


</x-front-logged-in-layout>
