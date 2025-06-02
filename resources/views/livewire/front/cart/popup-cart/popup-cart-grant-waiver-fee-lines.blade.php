@php
    use App\Accessors\Order\Cart\ServiceCarts;use App\Helpers\Front\DivinePriceHelper;
@endphp
@foreach($grantWaiverFeeLines as $line)
    @php
        $dPrice = DivinePriceHelper::getDivinePrice($line);
        $quantity = $line['quantity'];
    @endphp
    <tr
        wire:key="{{rand()}}"
        class="align-middle" x-data="{
                                quantity: {{$quantity}},
                                showQuantity: false,
                        }">
        <td class="pe-4">
            {{ __('front/order.deposit_pec') }}
            <b>{{$line->meta_info['grant_title']}}</b>
            <x-front.debugmark title="{{$line->shoppable_id}}"/>
        </td>
        <td>1</td>
        <td>{{$dPrice->ttc()}} €</td>
        <td>
            <button
                wire:click="removeWaiverFee"
                class="btn btn-link mt-1">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
