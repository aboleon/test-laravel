<div class="mb-3 row place-room-setup-row pb-3 mfw-line-separator" data-identifier="{{ $identifier }}">
    <div class="col-md-6">
        <div class="row">
            <div class="col-12">
                <x-mfw::input name="place_room_setup.name." label="Titre" :value="$setup->name"/>
                <x-mfw::validation-error field="place_room_setup.name.{{ $loop }}"/>
            </div>
            <div class="col-12 my-3">
                <x-mfw::number name="place_room_setup.capacity." label="Capacité" :value="$setup->capacity"/>
                <x-mfw::validation-error field="place_room_setup.capacity.{{ $loop }}"/>
            </div>
        </div>
        <x-mfw::simple-modal id="delete_place_room_setup"
                             class="btn btn-danger btn-sm mt-2"
                             title="Suppression d'une mise en place"
                             confirmclass="btn-danger"
                             confirm="Supprimer"
                             callback="deletePlaceRoomSetup"
                             :identifier="$identifier"
                             :modelid="$setup->id"
                             text="Supprimer"/>
    </div>
    <div class="col-md-6">
        <x-mfw::textarea name="place_room_setup.description." label="Description" :value="$setup->description"/>
    </div>
</div>

@pushonce('callbacks')
    <script>
        function ajaxPostDeletePlaceRoomSetup(result) {
            $(result.input.identifier).remove();
        }
    </script>
@endpushonce
@pushonce('js')
    <script src="{{ asset('js/placeroom.js') }}"></script>
@endpushonce
