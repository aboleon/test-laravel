@php
    use App\Helpers\Front\DivinePriceHelper;
@endphp
@foreach($stayLines as $stayLine)
    @php

        $dPrice = DivinePriceHelper::getDivinePrice($stayLine);
        $meta = $stayLine->meta_info;
        $nbNights = count($meta['price_per_night']);
        $hotelName = $meta['hotel_name'];
        $accommodationId = $meta['accommodation_id'];

        if ($nbNights > 1) {
            $headerLine = "$nbNights nuits à l'hôtel $hotelName";
        } else {
            $headerLine = "$nbNights nuit à l'hôtel $hotelName";
        }
        $sublines = [
            "<b>Arrivée</b> : " . $meta['date_start'],
            "<b>Départ (au matin)</b> : " . $meta['date_end'],
            "<b>Chambre</b> : " . strtoupper($meta['room_group_name']) . " - " . $meta['room_name'] . " (" . $meta['nb_person'] . " pers.)",
        ];

    @endphp
    <tr
        wire:key="{{rand()}}"
        class="align-middle">
        <td class="pe-4">
            {{$headerLine}}
            <x-front.debugmark title="ac_id={{$accommodationId}}"/>
            @foreach($sublines as $subline)
                <br>
                <span class="smaller">- {!! $subline !!}</span>
            @endforeach
        </td>
        <td></td>
        <td>
            @if(($isPecEligible && $stayLine->total_pec) or ($stayLine['meta_info']['amendable_amount']))
                @if ($stayLine['meta_info']['amendable'])
                    @php
                        $stayLine->total_ttc -= $stayLine['meta_info']['processing_fee_ttc'];
                    @endphp
                @endif
                <span class="text-decoration-line-through pe-2">
                    {{ \MetaFramework\Accessors\Prices::readableFormat(price:$stayLine->total_ttc, showDecimals: false) }}
                </span>
                @if ($isPecEligible && $stayLine->total_pec && !$stayLine['meta_info']['amendable_amount'])
                    {{ \MetaFramework\Accessors\Prices::readableFormat(price:$stayLine->total_ttc - $stayLine->total_pec, showDecimals: false) }}
                @endif

                @if ($stayLine['meta_info']['amendable'])
                    {{ \MetaFramework\Accessors\Prices::readableFormat(price:$stayLine->total_ttc - $stayLine['meta_info']['amendable_amount'], showDecimals: false) }}
                @endif

            @else
                {{ \MetaFramework\Accessors\Prices::readableFormat(price:$stayLine->total_ttc, showDecimals: false) }}
            @endif
        </td>
        <td>
            <button
                wire:click="removeStay({{$stayLine['id']}})"
                class="btn btn-link mt-1">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
