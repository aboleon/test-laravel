
<tr class="establishment-{{  $establishment_id }}"
    data-identifier="{{ $random }}">
    <td>{{$establishment->name}}</td>
    <td>{{$establishment->country->name}}</td>
    <td>{{$establishment->locality}}</td>
    <td>
        <input type="hidden" name="grant_establishment[establishment_id][]"
               value="{{ $establishment_id }}"/>
        <x-mfw::number min="0" name="grant_establishment[pax][]" :value="$pax"/>
    </td>
    <td>
        <x-mfw::simple-modal id="delete_grant_binded_establishment"
                             class="btn btn-danger btn-sm mt-2"
                             title="Suppression d'un établissement Grant"
                             confirmclass="btn-danger"
                             confirm="Supprimer"
                             callback="deleteGrantEstablishment"
                             :identifier="$random"
                             text="Supprimer"/>
    </td>
</tr>
