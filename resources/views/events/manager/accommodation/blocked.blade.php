<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2 fs-4">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Chambres bloquées</span>
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

        <div class="alert alert-danger d-none" id="blocked-errors">
            Veuillez corriger toutes les erreurs avant de continuer
        </div>

        @include('events.manager.accommodation.inc.rappel_contingent')

        <div id="blocked-messages" data-ajax="{{ route('ajax') }}"></div>

        <form method="post"
              action="{{ route('panel.manager.event.accommodation.rooms.blocked.update', [$event, $accommodation]) }}"
              id="wagaia-form">

            <h4 class="ms-1">Blocage de chambres</h4>
            <p>Le blockage se fait par date / catégorie de chambre. Le stock bloqué ne peut pas exécéder le <b>Restant</b> (voir tableau récapitulatif ci-dessus) + celui déjà saisi (si tel est le cas)</p>
            @csrf
            @method('PUT')
            <div id="messages" data-ajax="{{ route('ajax') }}"></div>
            <table class="table">
                <thead>
                <tr>
                    <th class="align-middle">Type de participants</th>
                    <th class="align-middle">Date</th>
                    <th class="align-middle">Catégorie</th>
                    <th class="align-middle">Stock bloqué</th>
                    <th class="align-middle">Dont Grant *<br><small>intervenants uniquement</small></th>
                    <th class="text-center align-middle">X</th>
                </tr>
                </thead>
                <tbody id="blocked-container" data-event-accommodation-id="{{ $accommodation->id }}">
                @if($blocked->isNotEmpty())
                    @foreach($blocked as $key => $group)
                        @for($i=0;$i<count($group);++$i)
                            <x-blocked-room :participationtypes="$participation_types"
                                            :row="$group[$i]"
                                            :roomgroups="$room_groups"
                                            :iteration="$i"
                                            :dates="$_REQUEST['GLOBALS']['contingent_dates'] ?? collect()"/>
                        @endfor
                    @endforeach
                @endif
                </tbody>
            </table>
        </form>

        <x-mfw::notice message="Aucune chambre bloquée n'est saise pour cet hébergement"
                       :class="'blocked-notice' .($blocked->isNotEmpty() ? ' d-none' : '')"/>

        @if ($accommodation->roomGroups->isNotEmpty() && $participation_types)
            <button class="btn btn-sm btn-success mt-3" id="add-blocked" type="button">
                <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
            </button>
        @else
            @if($accommodation->roomGroups->isEmpty())
                <x-mfw::alert message="Aucune chambre n'est configurée." type="danger rooms-notice" class="mt-2"/>
            @endif
            @if (!$participation_types)
                <x-mfw::alert message="Aucun type de participation n'est sélectionné pour cet hébergement"
                              type="danger rooms-notice" class="mt-2"/>
            @endif
        @endif
    </div>

    <template id="blocked-row">
        <x-blocked-room :participationtypes="$participation_types"
                        :row="new \App\Models\EventManager\Accommodation\BlockedRoom()" :roomgroups="$room_groups"
                        :dates="$_REQUEST['GLOBALS']['contingent_dates'] ?? collect()"/>
    </template>

    <x-mfw::simple-modal id="delete_blocked"
                         class="btn btn-danger btn-sm mt-2 d-none"
                         title="Suppression d'une ligne de chambres bloquées"
                         confirmclass="btn-danger"
                         confirm="Supprimer"
                         callback="ajaxDeleteBlockedRow"
                         text="Supprimer"/>

    @push('callbacks')
        <script src="{{ asset('js/eventmanager/blocked_rooms_callbacks.js') }}"></script>
    @endpush
    @push('js')
        <script src="{{ asset('js/eventmanager/blocked_rooms.js') }}"></script>
    @endpush
    <x-mfw::save-alert/>
</x-event-manager-layout>
