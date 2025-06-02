@php
    use App\Accessors\Order\Cart\ServiceCarts;use App\Helpers\Front\DivinePriceHelper;
@endphp
@foreach($serviceLines as $serviceLine)
    @php
        $dPrice = DivinePriceHelper::getDivinePrice($serviceLine);
        $quantity = $serviceLine['quantity'];
    @endphp
    <tr
            wire:key="{{rand()}}"
            class="align-middle" x-data="{
                                quantity: {{$quantity}},
                                showQuantity: false,
                        }">
        <td class="pe-4">
            {{$serviceLine->shoppable->title}}
            <x-front.debugmark title="{{$serviceLine->shoppable->id}}" />
        </td>
        <td class="d-none d-sm-block">
            <input
                    class="input-quantity"
                    type="number"
                    min="1"
                    x-model="quantity"
                    @change="showQuantity=true"
            >
            <button :style="{
                                            visibility: showQuantity ? 'visible' : 'hidden'
                                        }"
                    x-cloak
                    wire:click.prevent="updateServiceQuantity({{$serviceLine->shoppable->id}}, quantity); $wire.showCart=true;"
                    class="btn btn-link mt-1 ps-0">
                <i title="Valider" class="bi bi-arrow-clockwise"></i>
            </button>
        </td>
        <td>
            @php
                $serviceTtc = $dPrice->ttc();
            @endphp
            @if($isPecEligible && $serviceLine->total_pec)
                <span class="text-decoration-line-through smaller">{{$serviceTtc}} €</span>
                {{$serviceTtc - $serviceLine->total_pec}} €
            @else
                {{$serviceTtc}} €
            @endif

        </td>
        <td>
            <button
                    wire:click="updateServiceQuantity({{$serviceLine->shoppable->id}}, 0)"
                    class="btn btn-link mt-1">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
