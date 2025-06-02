<tr class="grant-binded-location-row" data-identifier="{{ $identifier }}">
    <td class="locality {{ $location->id && $location->type != 'locality' ? 'opacity-50' : '' }}">
        {{ $location->locality }}
    </td>
    <td class="country {{ $location->id && $location->type != 'country' ? 'opacity-50' : '' }}">
        @if ($location->type != 'continent')
            {{ \MetaFramework\Accessors\Countries::getCountryNameByCode($location->country_code) }}
        @endif
    </td>
    <td class="continent {{ $location->id && $location->type != 'continent' ? 'opacity-50' : '' }}">
        {{ \App\Accessors\Continents::getTranslatedName($location->continent) }}
    </td>
    @if($location->fields())
        @foreach($location->fields() as $field_key => $field_type)
            <td>
                @switch($field_type)
                    @case('number')
                        <x-mfw::number min="0" name="grant_binded_location[{{ $field_key }}][]"
                                       :value="$location->{$field_key}"/>
                        @break
                    @default
                        <x-mfw::input name="grant_binded_location[{{ $field_key }}][]"
                                      :value="$location->{$field_key}"/>
                @endswitch
            </td>
        @endforeach
    @endif
    <td class="text-end">
        <input type="hidden" name="grant_binded_location[country_code][]" value="{{ $location->country_code }}"
               class="input country_code"/>
        <input type="hidden" name="grant_binded_location[locality][]" value="{{ $location->locality }}"
               class="input locality"/>
        <input type="hidden" name="grant_binded_location[continent][]" value="{{ $location->continent }}"
               class="input continent"/>
        <input type="hidden" name="grant_binded_location[id][]" value="{{ $location->id }}"
               class="input id"/>
        <input type="hidden" name="grant_binded_location[type][]" value="{{ $location->type }}"
               class="input type"/>
        <input type="hidden" name="grant_binded_location[active][]" value="{{ $location->active }}"
               class="input active"/>


        <x-mfw::simple-modal id="delete_grand_binded_row"
                             class="btn btn-danger p-2 btn-sm d-flex align-items-center"
                             title="Suppression d'une localisation lié au GRANT"
                             confirmclass="btn-danger"
                             confirm="Supprimer"
                             callback="deleteGrantBindedLocation"
                             :identifier="$identifier"
                             :modelid="$location->id"
                             text='<i class="fa-solid fa-circle-xmark"></i>'/>
    </td>
</tr>

@pushonce('js')
    <script>

        function setContinent(result) {
            if (!result.hasOwnProperty('error')) {
                let row = $('#grant-binded-locations').find('.grant-binded-location-row').last();
                row.find('td.continent').text(result.continent.name).end().find('input.continent').val(result.continent.code)
            }
        }

        const gbl = {
            container: function () {
                return $('#grant-binded-locations');
            },
            template: function () {
                return $('template#grant-binded-location-row');
            },
            saved: function () {
                return $('#grant-binded-saved-locations tbody');
            },
            maps: function () {
                return this.container().find('.gmapsbar');
            },
            value: function (value) {
                return $.trim(this.maps().find('input.' + value).val());
            },
            guid: function () {
                return guid();
            },
            add: function () {
                $('#add-grant-binded-location').click(function () {
                    if (gbl.value('wa_geo_lon').length < 1) {
                        return false;
                    }
                    gbl.saved().append(gbl.template().html());
                    let last_row = gbl.container().find('.grant-binded-location-row').last(),
                        guid = gbl.guid();
                    let locality = gbl.value('locality'), addressType = gbl.value('address_type');
                    last_row.attr('data-identifier', guid);
                    last_row.find('a[data-modal-id=delete_grand_binded_row]').attr('data-identifier', guid).end()
                        .find('td.locality').text(locality).end()
                        .find('td.country').text(gbl.value('country')).end()
                        .find('td.continent').text(gbl.value('continent')).end()
                        .find('input.locality').val(locality).end()
                        .find('input.type').val(addressType).end()
                        .find('input.country_code').val(gbl.value('country_code'));

                    switch(addressType) {
                        case 'continent':
                            last_row.find('td.locality, td.country').addClass('opacity-50');
                            break;
                        case 'country':
                            last_row.find('td.locality, td.continent').addClass('opacity-50');
                            break;
                        default:
                            last_row.find('td.country, td.continent').addClass('opacity-50');
                    }

                    ajax('action=getContinentCodeByCountryCode' +
                        '&country_code=' + gbl.value('country_code') +
                        '&callback=setContinent' +
                        '&address_type=' + gbl.value('address_type') +
                        '&continent=' + gbl.value('continent'),
                        gbl.container());


                    $('#grant-binded-locations .g_autocomplete').val('').change();
                    $('.sellable_grant_binded_fields input').val('');
                });
            },
            init: function () {
                this.add();
            },
        };
        gbl.init();
    </script>
@endpushonce
