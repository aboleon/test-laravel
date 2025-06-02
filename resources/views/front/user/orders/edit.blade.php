@php

    use App\Accessors\Order\Orders;




@endphp
<x-front-logged-in-layout :event="$event">


    <a href="{{route('front.event.orders.index', $event)}}">
        <i class="bi bi-arrow-left"></i> Retour aux commandes
    </a>

    <h2 class="mt-5">Détails de la commande</h2>

    @if ($order_not_linked_to_event)

        <x-mfw::alert :message="__('front/order.order_not_linked_to_event')" />

    @else

        <livewire:front.order.order-details :order="$order" />
    @endif


</x-front-logged-in-layout>




