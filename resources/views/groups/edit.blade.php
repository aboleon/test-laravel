<x-backend-layout>
    <x-slot name="header">
        <h2>
            Edition d'un groupe
        </h2>

        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>
            @if ($data->id)
                <x-event-associator type="group" :id="$data->id" />
            @endif


            <x-back.topbar.separator />
            <x-back.topbar.edit-combo
                    routePrefix="panel.groups"
                    :model="$data"
                    itemName="le groupe {{ $data->name }}"
                    :wrap="false"
            />
        </div>
    </x-slot>
    <div class="shadow p-3 mb-5 bg-body-tertiary rounded">
        @include('groups.partials.edit_body')
    </div>

    @push('js')
        <script>
          activateEventManagerLeftMenuItem('groups');
        </script>
    @endpush

</x-backend-layout>
