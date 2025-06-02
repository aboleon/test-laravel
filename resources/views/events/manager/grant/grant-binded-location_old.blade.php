@for($i=0;$i< count((array)old('grant_binded_location.country_code'));++$i)
    @php
        $identifier = Str::random();
    @endphp
    <tr class="grant-binded-location-row" data-identifier="{{ $identifier }}">
        <td class="locality {{ old('grant_binded_location.type.'.$i) != 'locality' ? 'opacity-50' : '' }}">
            {{ old('grant_binded_location.'.$i.'.locality') }}
        </td>
        <td class="country {{ old('grant_binded_location.type.'.$i) != 'country' ? 'opacity-50' : '' }}">
            @if (old('grant_binded_location.type.'.$i) != 'continent')
                {{ \MetaFramework\Accessors\Countries::getCountryNameByCode(old('grant_binded_location.country_code.'.$i)) }}
            @endif
        </td>
        <td class="continent {{ old('grant_binded_location.type.'.$i) != 'continent' ? 'opacity-50' : '' }}">
            {{ \App\Accessors\Continents::getTranslatedName(old('grant_binded_location.continent.'.$i)) }}
        </td>
        @foreach((new \App\Models\EventManager\Grant\GrantLocation())->fields() as $field_key => $field_type)
            <td>
                @switch($field_type)
                    @case('number')
                        <x-mfw::number min="0" name="grant_binded_location[{{ $field_key }}][]"
                                       :value="old('grant_binded_location.'.$field_key.'.'.$i)"/>
                        @break
                    @default
                        <x-mfw::input name="grant_binded_location[{{ $field_key }}][]"
                                      :value="old('grant_binded_location.'.$field_key.'.'.$i)"/>
                @endswitch
            </td>
        @endforeach
        <td class="text-end">
            <input type="hidden" name="grant_binded_location[country_code][]" value="{{ old('grant_binded_location.country_code.'.$i) }}"
                   class="input country_code"/>
            <input type="hidden" name="grant_binded_location[locality][]" value="{{ old('grant_binded_location.locality.'.$i) }}"
                   class="input locality"/>
            <input type="hidden" name="grant_binded_location[continent][]" value="{{ old('grant_binded_location.continent.'.$i) }}"
                   class="input continent"/>
            <input type="hidden" name="grant_binded_location[id][]" value="{{ old('grant_binded_location.id.'.$i) }}"
                   class="input id"/>
            <input type="hidden" name="grant_binded_location[type][]" value="{{ old('grant_binded_location.type.'.$i) }}"
                   class="input type"/>
            <input type="hidden" name="grant_binded_location[active][]" value="{{ old('grant_binded_location.active.'.$i) }}"
                   class="input active"/>


            <x-mfw::simple-modal id="delete_grand_binded_row"
                                 class="btn btn-danger p-2 btn-sm d-flex align-items-center"
                                 title="Suppression d'une localisation lié au GRANT"
                                 confirmclass="btn-danger"
                                 confirm="Supprimer"
                                 callback="deleteGrantBindedLocation"
                                 :identifier="$identifier"
                                 :modelid="old('grant_binded_location.'.$i.'.id')"
                                 text='<i class="fa-solid fa-circle-xmark"></i>'/>
        </td>
    </tr>
@endfor
