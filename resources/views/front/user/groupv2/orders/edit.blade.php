<x-front-logged-in-group-manager-v2-layout :event="$event">

    <a href="{{route('front.event.group.orders', $event)}}">
        <i class="bi bi-arrow-left"></i> Retour aux commandes
    </a>

    <h2 class="mt-5">Détails de la commande</h2>

    @if ($orderAccessor->isFrontGroupOrder())
        @include('front.orders.groupmanager.order')
    @else
        <livewire:front.order.order-details :order="$order"/>
    @endif


</x-front-logged-in-group-manager-v2-layout>
