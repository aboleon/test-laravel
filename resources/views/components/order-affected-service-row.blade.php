<tr class="affected-service {{ $identifier }}">
    <td class="title"
        style="width: 40%;">{{ $services[$attribution->shoppable_id] ?? 'NC' }}</td>
    <td class="text-center service-date"
        style="width: 20%;">
        {{ $attribution?->service?->service_date }}
    </td>
    <td class="text-center qty" style="width: 20%;">{{ $attribution->quantity }}</td>
    <td class="text-center align-middle affected-date" style="width: 20%">
        {{ $attribution->created_at?->format('d/m/Y') }}

        <x-mfw::simple-modal id="delete_attribution_service_row"
                             class="btn mfw-bg-red btn-sm ms-2"
                             title="Suppression d'une attribution de service"
                             confirmclass="btn-danger"
                             confirm="Supprimer cette attribution"
                             callback="removeAttributionService"
                             :identifier="$identifier"
                             :modelid="$attribution->id"
                             text='<i class="fas fa-trash"></i>' />
    </td>
</tr>
