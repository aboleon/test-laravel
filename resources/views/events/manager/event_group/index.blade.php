<x-event-manager-layout :event="$event">
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
        <h2>
            Groupes participants
        </h2>
        </div>


        <div class="d-flex align-items-center gap-2" id="topbar-actions">
            <x-back.topbar.list-combo
                    :wrap="false"
                    :event="$event"
                    :show-create-route="false"
            />

            <button class="btn btn-sm btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#modal_add_eventgroup_panel">
                <i class="fa-solid fa-user-group"></i>
                Ajouter
            </button>
        </div>

    </x-slot>

    @include('events.manager.event_group.modal.add_panel')


    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages />
        <x-datatables-mass-delete
                model="event_groups"
                controller-path="EventManager\\EventGroup\\EventGroupController"
                model-path="EventGroup"
                deleted-message="Le groupe a été dissocié de cet événement."
        />
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}

        <script>
          $(document).ready(function() {
            // remove the tab cookie for edit page
            Cookies.set('mfw_tab_redirect_event_group', 'dashboard-tabpane-tab', {expires: 1});
          });
        </script>

    @endpush

    @pushonce('js')
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
    @endpushonce
</x-event-manager-layout>
