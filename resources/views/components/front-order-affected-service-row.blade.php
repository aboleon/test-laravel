<tr class="member member-{{ $member->id }}">
    <th colspan="4" class="border-0 pt-4 pb-2 text-dark">{{ $member->name }}</th>
</tr>

<tr class="{{ $attributions->isNotEmpty() ? 'd-none' :'' }} base-row member-{{$member->id}}">
    <td colspan="4">
        <x-mfw::alert type="warning" class="py-1 px-2 mx-0 my-1" message="Aucune affectation pour ce membre"/>
    </td>
</tr>
@if ($attributions->isNotEmpty())
    @foreach($attributions as $service_id => $attributed)
        @php
            $identifier = 'member-'.$member->id . ' affected-service '.\App\Enum\OrderCartType::SERVICE->value. '-'.$service_id;
        @endphp


        <tr class="text-center {{  $identifier }}">
            <td class="align-middle text-start service-name"
                style="width: 40%;">{{ $services[$service_id] }}
                {{--
                                @if ($attributed?->service?->service_date)
                                    <small>Valable jusqu'au {{ $attribution->service->service_date }}</small>
                                @endif
                --}}
            </td>
            <td class="align-middle qty"
                data-qty="{{ $attributed['quantity'] }}"
                data-service-id="{{ $service_id }}"
                data-event-contact-id="{{ $member->id }}"
                style="width: 20%;">{{ $attributed['quantity'] }}</td>
            <td class="align-middle affected-date" style="width: 20%">
                {{ $attributed['affected_date'] }}
            </td>
            <td class="align-middle">
                <x-mfw::simple-modal id="delete_attribution_service_row"
                                     class="btn btn-sm btn-primary m-0"
                                     title="Suppression d'une attribution de service"
                                     confirmclass="btn-danger"
                                     confirm="Supprimer cette attribution"
                                     callback="removeAttributionService"
                                     :identifier="$identifier"
                                     :text="__('mfw.delete')"/>
            </td>
        </tr>

    @endforeach
@endif
