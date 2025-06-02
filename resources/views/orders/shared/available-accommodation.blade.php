<style>
    .contingent-row del {
        opacity: 0.7;
        color: #b42757
    }
</style>
<h4 class="fw-bold text-dark">{{ $accommodation->hotel->name }}</h4>
<table class="table bg-white border">
    <thead>
    <tr>
        <th style="width: 140px">Date</th>
        <th style="width: 150px">Catégorie</th>
        <th style="width: 90px">Contingent</th>
        <th style="width: 110px">Bloqués</th>
        <th style="width: 90px">Réservées</th>
        <th style="width: 90px">Disponibles</th>
        <th>Type</th>
        <th class="text-center">Prix vente</th>
        <th class="text-center">Prix à payer</th>
        <th class="text-center">Éligible PEC</th>
        <th class="text-center">Montant PEC</th>
        <th>Presta liée</th>
        <th>En ligne</th>
    </tr>
    </thead>
    <tbody id="contigent-container"
           data-hotel-id="{{ $accommodation->id }}"
           data-account-type="{{ $account_type }}"
           data-taxroom="{{ ($accommodation->processing_fee ?: 0) }}"
           data-taxroom-net="{{  \MetaFramework\Accessors\VatAccessor::netPriceFromVatPrice(($accommodation->processing_fee ?: 0), ($accommodation->processing_fee_vat_id ?? $defaultVatId)) }}"
           data-taxroom-vat="{{ \MetaFramework\Accessors\VatAccessor::vatForPrice(($accommodation->processing_fee ?: 0), ($accommodation->processing_fee_vat_id ?? $defaultVatId)) }}"
           data-taxroom-vatid="{{ $accommodation->processing_fee_vat_id }}">

    @foreach($range as $date)
        @if (!array_key_exists($date, $availability->get('contingent')))
            <tr>
                <td colspan="13">
                    <x-mfw::alert type="warning" class="m-0"
                                  message="{{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }} - aucune disponibilité"/>
                </td>
            </tr>
            @continue
        @endif
        @foreach($availability->get('contingent')[$date] as $roomgroup => $contingent)
            @php
                $recap = $availability_recap->get($date, $roomgroup);
/*
                if ($date == '2025-03-03' && $roomgroup == 68) {
                    d($availability->getAvailability(),'getAvailability');
                    d($availability->getParticipationType(),'Part type');
                    d($availability->isGroup(),'Is Group');
                    d($recap, $date . ' - ' . $roomgroup);
                }
*/
                $isGroup = $account_type == \App\Enum\OrderClientType::GROUP->value;
                $thisGroup = $recap['this_group'];

                $readableDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
                $rowspan = count($availability->getRoomConfigs()[$date][$roomgroup]) ?? '';
                $row_id = Str::random();
                $roomgroupLabel = $availability->getRoomGroups()[$roomgroup]['name'] ?? '';

                $booked = $availability->get('booked.confirmed_individual.'.$date.'.'.$roomgroup.'.total', 0)
                        + $availability->get('booked.confirmed_groups.'.$date.'.'.$roomgroup.'.total', 0);

                $cancelled = $availability->get('booked.cancelled.by_date.'.$date.'.'.$roomgroup.'.total', 0);

                $temp_booked = $availability->get('booked.temp.'.$date.'.'.$roomgroup,0);
                // Account Type 'group'
                $thisGroupBlocked = $thisGroup['blocked'] ?? 0;
                $thisGroupBooked = $thisGroup['booked']['on_quota'] ?? 0;

                $services = $accommodation->service ? [$accommodation->service->id => $accommodation->service->name] : [];
                $rowspanTag = $rowspan ? " rowspan=".$rowspan :'';
            @endphp
            <tr class="contingent-row main-row {{ $row_id }}" data-room-group="{{ $roomgroup }}"
                data-identifier="{{ $row_id }}"
                data-vat-id="{{ $accommodation->vatId() }}"
                data-room-group-label="{{ $roomgroupLabel }}">
                <td class="rowspan align-top date {{ $loop->first ? '' : 'invisible' }}"
                    {{ $rowspanTag }} style="max-width: 100px;">
                    {{ $readableDate }}
                </td>
                <td class="rowspan align-top room_group" data-id="{{ $roomgroup }}" {{ $rowspanTag }}
                style="max-width: 160px">
                    {{ $roomgroupLabel }}
                </td>
                <td class="rowspan align-top total text-center"{{ $rowspanTag }}>
                    {!! $contingent !!}
                </td>
                <td class="rowspan align-top blocked-group"{{ $rowspanTag }}>
                    <small
                            class="d-flex justify-content-between"><span>Individuels :</span><span>{{ $availability->get('blocked')['individual'][$date][$roomgroup]['total'] ?? 0 }}</span></small>
                    <small
                            class="d-flex justify-content-between">
                        <span>Groupes :</span><span>{{ array_sum($recap['blocked']['groups'])}}</span></small>

                    @if($isGroup)
                        <small class="d-flex justify-content-between this-group"
                               data-contingent="{{ $thisGroupBlocked }}"
                               data-booked="{{ $thisGroupBooked }}"
                        ><span>Ce groupe :</span><span>{{ $thisGroupBlocked }}</span></small>
                    @endif
                </td>
                <td class="rowspan align-top booked text-center"{{ $rowspanTag }}>
                    {{ $booked - $cancelled}}
                    @if($temp_booked)
                        (+{{ $temp_booked }})
                    @endif
                    @if($isGroup && $thisGroupBooked)
                        <small class="d-flex justify-content-between mt-3"
                        ><span>Ce groupe :</span><span>{{ $thisGroupBooked }}</span></small>
                    @endif
                </td>
                <td class="rowspan align-top stock stock-{{ $roomgroup .' '. $date}} text-center" {{ $rowspanTag }}
                data-room-group="{{ $roomgroup }}"
                    data-stock="{{ $availability_summary[$date][$roomgroup] ?? 0 }}"
                    data-onquota="{{ $recap['on_quota'] }}"
                >
                    {!! $availability_summary[$date][$roomgroup] ?? 0 !!}
                </td>
                @if ($availability->getRoomConfigs()[$date][$roomgroup])
                    @if ($rowspan)
                        @include('orders.shared.accommodation-room-line', [
                                'room' => $availability->getRoomConfigs()[$date][$roomgroup][0],
                            ])
                    @endif
                @else
                    <td colspan="7">Aucune chambre configurée</td>
                @endif
            </tr>
            @if ($availability->getRoomConfigs()[$date][$roomgroup] && $rowspan > 1)
                @for($i=1;$i<$rowspan;++$i)
                    <tr class="subrow contingent-row {{ $row_id }}" data-room-group="{{ $roomgroup }}"
                        data-vat-id="{{ $accommodation->vatId() }}"
                        data-identifier="{{ $row_id }}"
                        data-room-group-label="{{ $roomgroupLabel }}">
                        @include('orders.shared.accommodation-room-line', [
                            'room' => $availability->getRoomConfigs()[$date][$roomgroup][$i],
                            ])
                    </tr>
                @endfor
            @endif

        @endforeach
    @endforeach

    </tbody>
</table>
