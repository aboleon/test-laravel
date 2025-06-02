@php use App\Models\FrontCartLine; @endphp
@if(count($stayLines) > 0)
    <div class="row">
        <div class="divine-secondary-color-text d-flex align-items-center fade show py-3 pe-2"
             role="alert">
            <i class="bi bi-house-fill fa-fw me-1"></i>
            {{ __('front/cart.accommodation') }}
        </div>
    </div>

    <table class="table table-sm">
        <tbody>
        @foreach($stayLines as $frontCartLine)
            @php
                /**
                * @var FrontCartLine $frontCartLine
                */
                $totalTtc = $frontCartLine->total_ttc;
                $totalPec = $frontCartLine->total_pec;
                $meta = $frontCartLine->meta_info;
                $dateStart = $meta['date_start'];
                $dateEnd = $meta['date_end'];
                $nbPerson = $meta['nb_person'];
                $roomName = $meta['room_name'];
                $roomGroupName = $meta['room_group_name'];
                $hotelName = $meta['hotel_name'];
                $pricePerNight = $meta['price_per_night'];
                $accompanyingDetails = $meta['accompanying_details'];
                $comment = $meta['comment'];
                $processingFeeTtc = $meta['processing_fee_ttc'];
                $nbNights = count($pricePerNight);


            @endphp
            <tr>
                <th class="text-dark">{{ trans_choice('front/accommodation.hotel',1) }}</th>
                <td>{{ $hotelName }}</td>
            </tr>
            <tr>
                <th class="text-dark">{{ trans_choice('front/accommodation.room',1) }}</th>
                <td>{{ strtoupper($roomGroupName) }} - {{ $roomName }}</td>
            </tr>
            <tr>
                <th class="text-dark">{{ __('front/cart.accommodation_dates') }}</th>
                <td>{{ str_replace(':','',__('front/accommodation.date_from')) .' ' . $dateStart .' '. strtolower(str_replace(':','',__('front/accommodation.date_to'))) .' '.  $dateEnd }}
                    ({{ __('front/accommodation.morning_departure') }})<br>
                    {{ $nbNights . ' '. trans_choice('front/order.overnight', $nbNights)}}
                </td>
            </tr>
            <tr>
                <th class="text-dark">{{ __('front/cart.accommodation_price') }}</th>
                <td>{{ $totalTtc - $processingFeeTtc }} €</td>
            </tr>
            <tr>
                <th class="text-dark">{{ __('front/cart.accommodation_application_fee') }}</th>
                <td>
                    {!! ($meta['amendable_amount'] ? '<span class="text-decoration-line-through smaller">'.$processingFeeTtc.' €</span> ' . 0 : $processingFeeTtc ) !!}
                    €
                </td>
            </tr>
            <tr>
                <th class="text-dark">{{ __('front/cart.accommodation_total_price') }}</th>
                <td>

                    @if($isPecEligible && $totalPec or ($meta['amendable_amount']))
                        <span class="text-decoration-line-through smaller">{{ $totalTtc }} €</span>
                        {{ $meta['amendable_amount']
                            ? $totalTtc - $totalPec - $meta['amendable_amount'] - $processingFeeTtc
                            : $totalTtc - $totalPec,
                        }}
                        €
                    @else
                        {{ $totalTtc }} €
                    @endif
                </td>
            </tr>
            <tr>
                <th class="text-dark">{{ __('front/cart.accommodation_nb_persons') }}</th>
                <td>{{ $nbPerson }}</td>
            </tr>
            @if($accompanyingDetails)
                <tr>
                    <th class="text-dark">{{ __('front/accommodation.col_accompany_details') }}</th>
                    <td>{{ $accompanyingDetails }}</td>
                </tr>
            @endif
            @if($comment)
                <tr>
                    <th class="text-dark">{{ __('front/accommodation.col_comments') }}</th>
                    <td>{{ $comment }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end gap-2 mt-2">
        <a class="btn btn-danger btn-sm"
           wire:click="removeStay({{ $frontCartLine->id }})"
           href="#">{{ __('front/cart.accommodation_delete') }}
            <x-front.livewire-ajax-spinner class="border-start"
                                           target="removeStay({{ $frontCartLine->id }})"/>
        </a>
    </div>
@endif
