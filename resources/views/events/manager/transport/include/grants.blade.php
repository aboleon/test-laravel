@php
    use App\Enum\AmountType;use MetaFramework\Accessors\Prices;
@endphp
@if (!$eventContactAccessor->isPecAuthorized() && !$eventContactAccessor->hasAnyTransportPec())

    <x-mfw::notice
        message="Ce contact n'est pas PEC. Remboursement libre à définir."/>

@else

    <h4 class="title-grants-available {{ $eventContactAccessor->hasAnyTransportPec() ? 'd-none' : '' }}">
        Grants disponibles</h4>
    <h4 class="title-grants-allocated {{ !$eventContactAccessor->hasAnyTransportPec() ? 'd-none' : '' }}">
        Grants alloués</h4>

    <button
        class="btn btn-sm btn-success {{ $eventContactAccessor->hasAnyTransportPec() ? 'd-none' : '' }}"
        type="button"
        data-id="{{ $transport->id }}"
        id="transport_check_grants">Vérifier le financement
        disponible
    </button>

    <div id="transport_grants_messages"></div>

    <table class="table table-sm mt-4"
           id="transport_available_grants">

        @if($eventContactAccessor->hasAnyTransportPec())
            <thead>
            <tr>
                <th>Grant</th>
                <th class="text-end">Type</th>
                <th class="text-end">Montant</th>
                <th class="text-end">A charge du participant</th>
                <th class="text-end">Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($eventContactAccessor->transportPec() as $entry)
                @php
                    $treshold = $entry->grant_type == AmountType::TAX->value ? $transport->price_after_tax : $transport->price_before_tax;
                $acceptable = min($treshold, $entry->total);
                @endphp
                <tr>
                    <td>{{ $entry->grant_title }}</td>
                    <td class="text-end">{{ strtoupper($entry->grant_type) }}</td>
                    <td class="text-end">{{ $entry->total }}</td>
                    <td class="text-end">{{ Prices::readableFormat($acceptable == $treshold ? 0 : $treshold - $acceptable ,'','.') }}</td>
                    <td class="text-end">{{ $entry->created_at }}</td>
                    <td class="text-end">
                        <button type="button"
                                data-id="{{ $entry->distribution_id }}"
                                class="remove-pec btn btn-danger btn-sm">
                            Supprimer
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        @endif


    </table>
@endif
