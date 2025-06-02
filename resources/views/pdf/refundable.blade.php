<x-pdf-layout>
    @push('title')
        <title>Facture</title>
    @endpush


    @push('css')
        {!!  csscrush_inline(public_path('front/css/pdf.css')) !!}
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

                <div class="title">Avoir</div>


                <table class="table-bordered table-two-rows">
                    <tr>
                        <td><b>DATE</b></td>
                    </tr>
                    <tr>
                        <td>{{ $refund->created_at->format('d/m/Y') }}</td>
                    </tr>
                </table>

                <div class="info-coordinates">
                    <b>Adresse de facturation :</b><br>
                    {!! $address !!}
                    @if($order->invoiceable->vat_number)
                        <br><br><b>TVA Intracommunautaire :</b><br>
                        <br>{{ $order->invoiceable->vat_number }}
                    @endif
                </div>

            </td>
        </tr>
    </table>


    <table class="table-semi-bordered table-items">
        <thead>
        <tr>

            <th style="padding-left:20px;width: 62%; text-align: left; vertical-align: middle; ">Désignation</th>
            <th>Date</th>
            <th>Prix HT</th>
            <th>Tva</th>
            <th>Total TTC</th>
        </tr>
        </thead>
        <tbody>
        @foreach($refund->items as $model)
            @php
                $net = \MetaFramework\Accessors\VatAccessor::netPriceFromVatPrice($model->amount, $model->vat_id);
                $vat = \MetaFramework\Accessors\VatAccessor::vatForPrice($model->amount, $model->vat_id);
                $totals[$model->vat_id][] = [
                    'net' => $net,
                    'ttc' => $model->amount,
                    'vat' => $vat
                ];
            @endphp
            <tr>
                <td style="text-align: left;padding-left:20px;">
                    {{ $model->object }}
                </td>
                <td>
                    {{  $model->date }}
                </td>
                <td class="net_price align-top">
                    {{ \MetaFramework\Accessors\Prices::readableFormat($net, '','.') }}

                </td>
                <td class="align-top">
                    {{  \MetaFramework\Accessors\VatAccessor::rate($model->vat_id) }}%
                </td>
                <td class="align-top">
                    {{ \MetaFramework\Accessors\Prices::readableFormat($model->amount, '','.') }}
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
    @php
        $sums = collect($totals)->flatten(1)->reduce(function ($carry, $item) {
        $carry['net'] += $item['net'];
        $carry['ttc'] += $item['ttc'];
        return $carry;
    }, ['net' => 0, 'ttc' => 0]);
    @endphp

    <table class="table-bottom">
        <tr>
            <td style="width: 80%"></td>
            <td>
                <table class="table-recap">
                    <tr>
                        <td>Total HT</td>
                        <td>{{ \MetaFramework\Accessors\Prices::readableFormat($sums['net'], 'EUR', '.') }}</td>
                    </tr>

                    @foreach($totals as $vat_id => $subtotal)
                        <tr>
                            <td>
                                TVA {{ \MetaFramework\Accessors\VatAccessor::readableArrayList()[$vat_id] }}</td>
                            <td>{{ \MetaFramework\Accessors\Prices::readableFormat(array_sum(array_column($subtotal,'vat')), 'EUR', '.') }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td>Total TTC</td>
                        <td>{{ \MetaFramework\Accessors\Prices::readableFormat($sums['ttc'], 'EUR', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @include('pdf/inc/divine-address')
</x-pdf-layout>
