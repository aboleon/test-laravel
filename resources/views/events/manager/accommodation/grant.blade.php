<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2 fs-4">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Chambres Grant</span>
        </h2>
        <x-back.topbar.edit-combo
                :event="$event"
                :index-route="route('panel.manager.event.accommodation.index', $event)"
                :use-create-route="false"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        @include('events.manager.accommodation.tabs')

        <div class="alert alert-danger d-none" id="grant-errors">
            Veuillez corriger toutes les erreurs avant de continuer
        </div>

        @include('events.manager.accommodation.inc.rappel_contingent', ['show_blocked'=>true])

        <form method="post" action="{{ route('panel.manager.event.accommodation.rooms.grant.update', [$event, $accommodation]) }}" id="wagaia-form">

            <h4 class="ms-1">Gestion des Grant</h4>

            @csrf
            @method('PUT')
            <div id="messages" data-ajax="{{ route('ajax') }}"></div>
            <table class="table">
                <thead>
                <tr>
                    <td colspan="3"></td>
                    <th colspan="5" class="bg-body-secondary text-center">GRANT</th>
                </tr>
                <tr>
                    <th class="align-middle">Date</th>
                    <th class="align-middle">Catégorie</th>
                    <th class="align-middle">Stock général disponible</th>
                    <th class="align-middle">Bloqué</th>
                    <th class="align-middle text-center">Réservé</th>
                    <th class="align-middle text-center">En attente</th>
                    <th class="align-middle text-center">Restant</th>
                    <th class="text-center align-middle">Actions</th>
                </tr>
                </thead>
                <tbody id="grant-container">
                @if($accommodation->grant->isNotEmpty())
                    @foreach($accommodation->grant as $row)
                        <x-grant-line :row="$row"
                                      :roomgroups="$room_groups"
                                      :iteration="$loop->index"
                                      :dates="$_REQUEST['GLOBALS']['contingent_dates']"/>
                    @endforeach
                @endif
                </tbody>
            </table>
        </form>

        <x-mfw::notice message="Aucune ligne de gestion Grant n'est saise pour cet hébergement" :class="'grant-notice' .($accommodation->grant->isNotEmpty() ? ' d-none' : '')"/>

        <button class="btn btn-sm btn-success mt-3" id="add-grant" type="button">
            <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
        </button>
    </div>


    <template id="grant-row">
        <x-grant-line :row="new \App\Models\EventManager\Accommodation\Grant()"
                      :roomgroups="$room_groups"
                      :dates="$_REQUEST['GLOBALS']['contingent_dates'] ?? collect()"/>
    </template>

    <x-mfw::simple-modal id="delete_grant"
                         class="btn btn-danger btn-sm mt-2 d-none"
                         title="Suppression d'une ligne de chambres bloquées"
                         confirmclass="btn-danger"
                         confirm="Supprimer"
                         callback="ajaxDeleteBlockedRow"
                         text="Supprimer"/>

    <x-mfw::save-alert />
</x-event-manager-layout>
