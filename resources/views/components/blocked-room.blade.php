<tr class="blocked-row {{ $identifier . ($iteration < 1 ? ' main-row' : ' subrow') }}" data-row="{{ $identifier }}" data-group="{{ $row->group_id }}">
    <td class="participation_type" data-recorded="{{ $row->participation_type }}">
        <div {!! $iteration > 0 ? ' class="d-none"' : ''!!}>
            <x-participation-types
                name="{{ $row->id ? $row->group_id.'['.$row->id.'][participation_type]' : 'participation_type' }}"
                :subset="$participationtypes"
                :affected="explode(',',$row->participation_type)"
                :filter="true"/>
        </div>
    </td>
    <td class="align-top">
        <x-mfw::datepicker name="{{ $row->id ? $row->group_id.'['.$row->id.'][date]' : 'date' }}" :value="$row->date"
                           :required="true"
                           :config="$dates->isNotEmpty() ? 'minDate='.$dates->first().',maxDate='.$dates->last() : ''"/>
    </td>
    <td class="align-top">
        <x-mfw::select name="{{ $row->id ? $row->group_id.'['.$row->id.'][room_group_id]' : 'room_group_id' }}"
                       :affected="$row->room_group_id" :values="$roomgroups" class="room-group" :nullable="false"/>
    </td>
    <td class="align-top">
        <x-mfw::number name="{{ $row->id ? $row->group_id.'['.$row->id.'][total]' : 'total' }}" min="1" :params="['data-prevalue' => $row->total]"
                       :value="$row->total" :required="true" class="day_stock"/>
    </td>
    <td class="grant bg-white align-top">
        <div{!! ($row->id && ($row->participation_group != 'orator' && !in_array($row->participation_type, $orators))) ? ' style="visibility:hidden"' : '' !!}>
            <x-mfw::number name="{{ $row->id ? $row->group_id.'['.$row->id.'][grant]' : 'grant' }}" min="0"
                           :value="$row->grant"/>
            <input type="hidden" class="key group" value="{{  $row->id ? $row->group_id : null }}" name="group[]"/>
            <input type="hidden" value="{{ $row->id }}"
                   name="{{ $row->id ? $row->group_id.'['.$row->id.'][id]' : 'id' }}"/>
        </div>
    </td>
    <td class="deletable align-top" style="width: 120px">
        <button type="button" class="btn btn-sm btn-success w-100 add-subline{{ $iteration > 0 ? ' d-none' : '' }}">
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
