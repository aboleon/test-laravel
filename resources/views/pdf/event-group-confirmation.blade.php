<x-pdf-layout>
    @push('title')
        <title>{{ $documentTitle }}</title>
    @endpush

    @push('css')
        {!!  csscrush_inline(public_path('front/css/pdf.css')) !!}
        <style>
            .underline {
                width: 100%;
                display:block;
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

                <div class="title">{{ $documentTitle }}</div>

                <table class="table-bordered table-two-rows">
                    <tr>
                        <td><b>DATE</b></td>
                    </tr>
                    <tr>
                        <td>{{now()->format('d/m/Y')}}</td>
                    </tr>
                </table>

                <div class="info-coordinates">
                    <b>{{__('front/ui.my_personal_info')}} :</b><br />
                    {{$group->company}} {{$group->name}}<br />
                    {!! $address !!}
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
    <br />
    <table class="table-semi-bordered table-items">
        <thead>
        <tr>
            <th>{{ __('front/event.group_confirmation.attribution') }}</th>
            <th style="width: 62%;text-align: left;padding-left: 20px">{{ $letter['product_name'] }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($attributedEventContacts as $eventContact)
            @if($eventContact->serviceAttributions->isNotEmpty())
                @foreach($eventContact->serviceAttributions as $attribution)
                    <x-confirmation-group-service-row :sellable="$attribution->service" :eventContact="$eventContact" />
                @endforeach
            @endif
            @php
                //dd($eventContact);
            @endphp
            @if($eventContact->accommodationAttributionsRelation->isNotEmpty())
                @foreach($eventContact->accommodationAttributionsRelation as $attribution)
                    @php
                        //dd($attribution->room->group->accommodation);
                    @endphp
                    <x-confirmation-group-accommodation-row :attribution="$attribution" :hotels="$hotels" :eventContact="$eventContact" />
                @endforeach
            @endif


        @endforeach
        </tbody>
    </table>

    @include('pdf/inc/divine-address')
</x-pdf-layout>
