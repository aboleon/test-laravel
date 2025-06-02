@php
    use App\Accessors\Order\Orders;
@endphp
<x-front-logged-in-layout :event="$event">


    <a href="{{route('front.event.group.orders', $event)}}">
        <i class="bi bi-arrow-left"></i> Retour aux commandes
    </a>

    <h2 class="mt-5">Détails de la commande</h2>


    <livewire:front.order.order-details :order="$order"  />



</x-front-logged-in-layout>




