@php
    use App\Accessors\Front\FrontCartAccessor;
    use App\Helpers\Front\Cart\FrontGroupCart;
    $groupCart = FrontGroupCart::getInstance($eventContact);
@endphp
<x-front-logged-in-group-manager-layout :event="$event">
    <x-front.session-notifs/>
    <livewire:front.cart.inline-group-cart :eventGroup="$eventGroup"
                                           :eventContact="$eventContact"/>
</x-front-logged-in-group-manager-layout>
