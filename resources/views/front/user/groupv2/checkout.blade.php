@php
    use App\Accessors\Front\FrontCartAccessor;
    use App\Helpers\Front\Cart\FrontGroupCart;
    $groupCart = FrontGroupCart::getInstance($eventContact);
@endphp
<x-front-logged-in-group-manager-v2-layout :event="$event">
    <x-front.session-notifs/>
    <h3 class="main-title">Mon panier de groupe</h3>
    <livewire:front.cart.inline-group-cart :eventGroup="$eventGroup"
                                           :eventContact="$eventContact"/>


</x-front-logged-in-group-manager-v2-layout>
