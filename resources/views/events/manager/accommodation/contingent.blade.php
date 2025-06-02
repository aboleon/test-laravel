<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2 fs-4">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Contingent</span>
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


        <form method="post"
              action="{{ route('panel.manager.event.accommodation.rooms.contingent.update', [$event, $accommodation]) }}"
              id="wagaia-form">
            @csrf
            @method('PUT')
            <div id="messages" data-ajax="{{ route('ajax') }}"></div>
            <table class="table">
                <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Contingent</th>
                    <th>Type</th>
                    <th>Prix vente</th>
                    <th>Prix achat</th>
                    <th>Éligible PEC</th>
                    <th>Montant PEC</th>
                    <th>Presta liée</th>
                    <th>En ligne</th>
                    <th class="text-center">X</th>
                </tr>
                </thead>
                <tbody id="contigent-container">
                @if ($accommodation->contingent->isNotEmpty())
                    @foreach($accommodation->contingent->sortBy(['date','room_group_id']) as $item)
                        @php
                            $row_id = Str::random();
                            $rowspan = $item->configs->count();
                            $services = $accommodation->service ? [$accommodation->service->id => $accommodation->service->name] : [];

                            // Delete orphans
                            if  (!$rowspan) {
                                $item->delete();
                                continue;
                            }
                        @endphp
                        <tr class="contingent-row {{ $row_id }}">
                            <td class="rowspan align-top" rowspan="{{ $rowspan }}" style="max-width: 160px">
                                <input type="hidden" class="key" name="row_key[]" value="{{ $row_id }}"/>
                                <input type="hidden" class="key" name="{{ $row_id }}[id]" value="{{ $item->id }}"/>
                                <x-mfw::select name="{{ $row_id }}[room_group_id]" :values="$room_groups"
                                               :affected="$item->room_group_id" class="room-group"
                                               :params="['data-room_group_id' => $item->room_group_id]"/>
                                <x-front.debugmark title="gid={{ $item->room_group_id }}"/>
                            </td>
                            <td class="rowspan align-top" rowspan="{{ $rowspan }}" style="max-width: 100px">
                                <x-mfw::datepicker name="{{ $row_id }}[date]" :required="true" :value="$item->date"
                                                   :params="['data-date' => $item->date]"/>
                            </td>
                            <td class="rowspan align-top" rowspan="{{ $rowspan }}" style="max-width: 80px">
                                <x-mfw::number name="{{ $row_id }}[total]" min="1" :required="true"
                                               :value="$item->total"/>
                            </td>
                            @if ($rowspan)
                                <x-contingent-config :row="$row_id" :config="$item->configs[0]" :services="$services"
                                                     :rooms="$rooms" :rowspan="$rowspan" :deletable="true"/>
                            @endif
                        </tr>
                        @if ($rowspan > 1)
                            @for($i=1;$i<$rowspan;++$i)
                                <tr class="subrow contingent-row {{ $row_id }}">
                                    <x-contingent-config :row="$row_id" :config="$item->configs[$i]"
                                                         :services="$services" :rooms="$rooms"/>
                                </tr>
                            @endfor
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </form>
        <div id="contingent-js-messages">
            <x-mfw::alert
                message="Une telle configuration chambres / date existe déjà. Vous ne pourrez pas sauvegarder en l'état."
                class="duplicate d-none"/>
        </div>

        <x-mfw::notice message="Aucune contingent n'est saisi pour cet hébergement"
                       :class="'contingent-notice ' . ($accommodation->contingent->isEmpty() ? '' : 'd-none')"/>
        @if ($accommodation->roomGroups->isNotEmpty())
            <button class="btn btn-sm btn-success mt-3" id="add-contingent" type="button">
                <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
            </button>
        @else
            <x-mfw::alert message="Aucune chambre n'est configurée." type="danger rooms-notice" class="mt-2"/>
        @endif
    </div>

    <template id="contingent-row" data-published="{{$accommodation->published}}">
        <tr class="contingent-row">
            <td class="rowspan align-top">
                <input type="hidden" class="key" name="row_key[]"/>
                <x-mfw::select name="room_group_id" :values="$room_groups" class="room-group"/>
            </td>
            <td class="rowspan align-top" style="max-width: 100px">
                <x-mfw::datepicker name="date" :required="true" config="defaultDate={{ $event->starts }}"/>
            </td>
            <td class="rowspan align-top" style="max-width: 80px">
                <x-mfw::number name="total" min="1" :required="true"/>
            </td>
        </tr>
    </template>

    @php
        $accommodationRoomGroups = $accommodation->roomGroups->load('rooms')->reject(fn($item) => $item->rooms->isEmpty());
    @endphp
    @if ($accommodationRoomGroups->isNotEmpty())
        @foreach($accommodationRoomGroups as $group)
            <template id="template-room-group-{{ $group->id }}">
                {!! $group->rooms->map(fn($item) => '<div data-room-id="'.$item->id.'">'.\App\Accessors\Dictionnaries::entry('type_chambres', $item->room_id)->name . ' x ' .$item->capacity.'</div>')->join('') !!}
            </template>
        @endforeach
    @endif

    <template id="accommodation-service">
        <option value="">Aucune</option>
        @if($accommodation->service)
            <option value="{{ $accommodation->service->id }}">{{ $accommodation->service->name }}</option>
        @endif
    </template>

    <x-mfw::simple-modal id="delete_contingent"
                         class="btn btn-danger btn-sm mt-2 d-none"
                         title="Suppression d'une ligne de contingent"
                         confirmclass="btn-danger"
                         confirm="Supprimer"
                         callback="ajaxDeleteContingentRow"
                         text="Supprimer"/>

    <x-mfw::save-alert/>

    @push('css')
        <style>
            .duplicate-row {
                border-left: 10px inset #c72323 !important;
            }
        </style>
    @endpush

    @push('js')
        <script src="{{ asset('js/eventmanager/contingent.js') }}"></script>
    @endpush

</x-event-manager-layout>
