<x-pdf-layout>
    @push('title')
        <title>{{ $letter['title'] }}</title>
    @endpush

    @push('css')
        {!!  csscrush_inline(public_path('front/css/pdf.css')) !!}
        <style>
            .underline {
                width: 100%;
                display: block;
                padding-bottom: 5px;
                border-bottom: 1px solid #000;
            }

            .unpaid td {
                color: #e43737;
            }

        </style>
    @endpush

    <table>
        <tr style="height:360px;">
            <td class="logo-container">

                <img class="logo-divine"
                     src="{{  public_path('assets/pdf/logonew.jpg') }}"
                     alt="divine logo">

                @if ($banner)
                    <img
                        src="{{  public_path($banner) }}"
                        style="width: 70%; margin-left: 5%; margin-top: 10%"

                        alt="congrès logo">
                @endif
            </td>
            <td class="header-info">

                <div class="title">{{ $letter['title'] }}</div>

                <table class="table-bordered table-two-rows">
                    <tr>
                        <td><b>DATE</b></td>
                    </tr>
                    <tr>
                        <td>{{now()->format('d/m/Y')}}</td>
                    </tr>
                </table>

                <div class="info-coordinates">
                    <b>{{__('front/ui.my_personal_info')}} :</b><br/>
                    {{ $eventContact->account->names() }}
                </div>

            </td>
        </tr>
    </table>
    <table>
        <tbody>
        <tr>
            <td>{!! $letter['body'] !!}</td>
        </tr>
        </tbody>
    </table>
    <br/>
    <table class="table-semi-bordered table-items">
        <thead>
        <tr>
            <th style="width: 62%;text-align: left;padding-left: 20px">{{ $letter['product_name'] }}</th>
            <th>{{ __('ui.quantity') }}</th>
            <th>{{ __('front/order.price_ht') }}</th>
            <th>{{ __('mfw-sellable.vat.label') }}</th>
            <th>{{ __('front/order.total_amount') }}</th>
        </tr>
        </thead>
        <tbody>
        @if($paid_services->count() || $paid_hotels->count())
            <tr>
                <td><span class="underline">{!! $letter['paid_title'] !!}</span></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($paid_services as $stockable)
                @php
                    $orderAccessor = new \App\Accessors\OrderAccessor($stockable->order);
                @endphp
                <x-invoice-row-service :cart="$stockable"
                                       :order-accessor="$orderAccessor"
                                       :services="$services"/>
            @endforeach

            @foreach($paid_hotels as $hotel)
                @php
                    $orderAccessor = new \App\Accessors\OrderAccessor($hotel->order);
                @endphp
                <x-invoice-row-accommodation :cart="$hotel"
                                             :order-accessor="$orderAccessor"
                                             :hotels="$hotels"
                                             :amendedorder="$hotel->order->amendedOrder"/>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endif

        @if($unpaid_services->count() || $unpaid_hotels->count())
            <tr>
                <td><span class="underline">{!! $letter['unpaid_title'] !!}</span></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($unpaid_services as $stockable)
                @php
                    $orderAccessor = new \App\Accessors\OrderAccessor($stockable->order);
                @endphp
                <x-invoice-row-service :cart="$stockable"
                                       :order-accessor="$orderAccessor"
                                       :services="$services"
                                       :isUnpaid="true"
                />
            @endforeach

            @foreach($unpaid_hotels as $hotel)
                @php
                    $orderAccessor = new \App\Accessors\OrderAccessor($hotel->order);
                @endphp
                <x-invoice-row-accommodation :cart="$hotel"
                                             :order-accessor="$orderAccessor"
                                             :hotels="$hotels"
                                             :amendedorder="$hotel->order->amendedOrder"
                                             :isUnpaid="true"
                />
            @endforeach
        @endif
        </tbody>
    </table>

    @if ($attributed->isNotEmpty())
        <table class="table-semi-bordered table-items">
            @foreach($attributed as $paid_by => $data)
                <thead>
                <tr class="unpaid">
                    <th style="width: 35%;text-align: left;padding-left: 20px">{!! $letter['order_by'].$paid_by !!}</th>
                    <th style="text-align: left;">{{ $letter['product_name'] }}</th>
                    <th style="text-align: left;">{{ $letter['attributed_content'] }}</th>
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr class="unpaid">
                        <td style="text-align: left;">{{ \App\Enum\OrderCartType::translated($item['type']) }}</td>
                        <td style="text-align: left;">{{ $item['title'] }}</td>
                        <td style="text-align: left;">{!!  $item['text']  !!}</td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    @endif

    @include('pdf/inc/divine-address')
</x-pdf-layout>
