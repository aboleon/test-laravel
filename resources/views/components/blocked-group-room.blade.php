<tr class="blocked-row {{ ($iteration == 1 ? 'main-row ' : ' ') . $identifier }}" data-row="{{ $identifier }}" data-group="{{ $row->group_key }}">
    <td class="hotel"{!! $iteration > 1 ? ' style="visibility:hidden"' : ''!!}>
        <x-mfw::select name="{{ $row->id ? $row->group_key.'['.$row->id.'][hotel_id]' : 'hotel_id' }}" :values="$hotels"
                       :affected="$row->event_accommodation_id"/>
    </td>
    <td class="dates" data-affected="{{ $row->getRawOriginal('date') }}">
        <x-mfw::select name="{{ $row->id ? $row->group_key.'['.$row->id.'][date]' : 'date' }}" :values="[]"
                       :affected="null"/>
    </td>
    <td class="room-groups" data-affected="{{ $row->room_group_id }}">
        <x-mfw::select name="{{ $row->id ? $row->group_key.'['.$row->id.'][room_group_id]' : 'room_group_id' }}"
                       :affected="null" :values="[]" class="room-group" :nullable="false"/>
    </td>
    <td class="available"></td>
    <td class="blocking">
        <x-mfw::number name="{{ $row->id ? $row->group_key.'['.$row->id.'][total]' : 'total' }}" min="1"
                       :value="$row->total" :required="true" class="day_stock"/>
    </td>
    <td class="deletable" style="width: 120px">
        <input type="hidden" class="key group" value="{{  $row->id ? $row->group_key : null }}" name="group[]"/>
        <input type="hidden" value="{{  $row->id }}" name="{{ $row->id ? $row->group_key.'['.$row->id.'][id]' : 'id' }}"/>

        <button type="button" class="btn btn-sm btn-success w-100 add-subline{{ $iteration > 1 ? ' d-none' : '' }}">
            <i class="fas fa-plus" style="font-size: smaller"></i> Sous-ligne
        </button>
        <x-mfw::simple-modal id="delete_blocked"
                             class="btn btn-danger btn-sm mt-1 w-100"
                             title="Suppression d'une ligne de chambres bloqués"
                             confirmclass="btn-danger"
                             confirm="Supprimer"
                             callback="ajaxDeleteBlockedRow"
                             :identifier="$identifier"
                             :modelid="$row->id"
                             text='Supprimer'/>
    </td>
</tr>
