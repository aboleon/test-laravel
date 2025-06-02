
<tr class="affected-service {{ $identifier }}">
    <td class="title" style="width: 40%;">

        @if ($attribution->id)
            <span class="fw-bold d-block">{!! ($room->room->name  .' x '.$room->capacity .' / '. $room->group->name ?? 'NC') !!}</span>
            {{ $cart->eventHotel->hotel->name .' ' . $cart->eventHotel->hotel->stars .'*' }}
        @endif
        </td>
    <td class="text-center service-date" style="width: 20%;">{{ $cart->date?->format('d/m/Y') }}</td>
    <td class="text-center qty" style="width: 20%;">{{ $attribution->id ? $attribution->quantity : '' }}</td>
    <td class="text-center affected-date" style="width: 20%;">
        {{ $attribution->created_at?->format('d/m/Y') }}
        <x-mfw::simple-modal id="delete_attribution_accommodation_row"
                             class="btn mfw-bg-red btn-sm ms-2"
                             title="Suppression d'une attribution de chambre"
                             confirmclass="btn-danger"
                             confirm="Supprimer cette attribution"
                             callback="removeAttributionAccommodation"
                             :identifier="$identifier"
                             :modelid="$attribution->id"
                             text='<i class="fas fa-trash"></i>' />
    </td>
</tr>
