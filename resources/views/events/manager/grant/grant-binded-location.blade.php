@push('css')
    <style>
        .sellable_grant_binded_fields {
            display: none;
        }

        .gmapsbar .locationField {
            margin-bottom: 0;
        }
    </style>
@endpush
<div id="grant-binded-locations" class="grant_deposit">
    <p class="text-secondary">
        <i class="bi bi-exclamation-triangle-fill" style="color: var(--base-1);font-size: 16px"></i>
        L'application de la caution GRANT dépend de :
    </p>


    <div class="row align-items-center">
        <div class="col-sm-10">
            <x-mfw::google-places :geo="new \MetaFramework\Models\Dummy\Address()"
                                  :params="['types' => ['locality','country','continent']]"
                                  field="sellable_grant_binded"
                                  placeholder="Tapez le nom d'une localité, un pays ou un continent"/>
        </div>
        <div class="col-sm-2">
            <button class="btn btn-sm btn-success" id="add-grant-binded-location" type="button">
                <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
            </button>
        </div>
    </div>

    <table id="grant-binded-saved-locations" class="table text-dark table-bordered mt-2">
        <thead>
        <tr>
            <th>Localité</th>
            <th>Pays</th>
            <th>Continent</th>
            <th>Montant max remboursement transport</th>
            <th>Pax</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        @if ($errors->any())
            @include('events.manager.grant.grant-binded-location_old')
        @else

            @if ($data->id)
                @forelse($data->locations as $location)
                    <x-grant-binded-location :location="$location"/>
                @empty
                @endforelse
            @endif
        @endif
        </tbody>
    </table>
    <div id="grant_locations_messages" data-ajax="{{ route('ajax') }}"></div>
</div>

<template id="grant-binded-location-row">
    <x-grant-binded-location :location="new \App\Models\EventManager\Grant\GrantLocation()"/>
</template>

@push('callbacks')
    <script>
        function ajaxPostDeleteGrantBindedLocationRow(result) {
            $(result.input.identifier).remove();
        }
    </script>
@endpush
@push('js')
    <script>
        function deleteGrantBindedLocation() {
            $('.delete_grand_binded_row').off().on('click', function () {

                $('.messages').html('');
                let id = $(this).attr('data-model-id'),
                    identifier = '.grant-binded-location-row[data-identifier=' + $(this).attr('data-identifier') + ']';
                $('#mfw-simple-modal').find('.btn-cancel').trigger('click');
                console.log(id, identifier, (id.length < 1 || isNaN(id)));
                if (id.length < 1 || isNaN(id)) {
                    $(identifier).remove();
                } else {
                    ajax('action=removeGrantBindedLocationRow&model=GrantLocation&id=' + Number(id) + '&identifier=' + identifier, $('#grant_locations_messages'));
                }
            });
        }
    </script>
@endpush
