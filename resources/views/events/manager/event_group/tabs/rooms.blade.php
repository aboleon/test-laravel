<div class="tab-pane fade" id="rooms-tabpane" role="tabpanel" aria-labelledby="rooms-tabpane-tab">

    {{--<div class="d-flex gap-2 mb-3 mt-4">
        <a class="btn btn-sm btn-green"
           href="{{ route('panel.manager.event.orders.create', ['event' => $event, 'group' => $eventGroup]) }}">
            <i class="fa-solid fa-plus"></i>
            Nouvelle commande
        </a>
    </div>--}}
    <table class="table">
        <thead>
        <tr>
            <th>Hôtel</th>
            <th>Date</th>
            <th>Catégorie</th>
            <th>Stock disponible</th>
            <th>Stock bloqué</th>
            <th></th>
        </tr>
        </thead>
        <tbody id="blocked_group_rooms" data-event="{{ $event->id }}" data-group="{{ $eventGroup->group_id }}"
               data-event-group-id="{{ $eventGroup->id }}">
        @if($eventGroup->blockedRooms->isNotEmpty())
            @php
                $groups = $eventGroup->blockedRooms->groupBy('group_key');
            @endphp
            @foreach($groups as $group)
                @foreach($group as $subset)
                    <x-blocked-group-room :row="$subset" :accommodation="$hotels" :iteration="$loop->iteration"/>
                @endforeach
            @endforeach
        @endif
        </tbody>
    </table>

    <x-mfw::notice message="Aucune chambre bloquée n'est saise pour ce groupe"
                   :class="'mb-3 blocked-notice' .($eventGroup->blockedRooms->isNotEmpty() ? ' d-none' : '')"/>
    @if ($hotels->isNotEmpty())
        <div class="row">
            <div class="col-6">
                <button class="btn btn-sm btn-success" id="add-blocked" type="button">
                    <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
                </button>
            </div>
            <div class="col-6 text-left text-sm-end">
                <button type="submit" class="btn btn-sm btn-warning mx-2" id="save_blocked_group_rooms">
                    <i class="fa-solid fa-check"></i>
                    Enregistrer
                </button>
            </div>
        </div>
    @else
        <x-mfw::alert message="Aucune chambre n'est configurée." type="danger rooms-notice"/>
    @endif

    <div id="blocked_group_room_messages" data-ajax="{{ route('ajax') }}"></div>

    <template id="blocked-row">
        <x-blocked-group-room :row="new \App\Models\EventManager\Groups\BlockedGroupRoom()"
                              :accommodation="$hotels"/>
    </template>

    @foreach($hotels as $hotel)
        @php
            $availability = (new \App\Accessors\EventManager\Availability())
                ->setEventAccommodation($hotel)
                ->setEventGroupId($eventGroup->id);
              $availability_recap = $availability->getAvailability();
              $availability_roomgroups = $availability->getRoomGroups();
        @endphp
        <div id="bgr-hotel-{{ $hotel->id }}" data-name="{{$hotel->hotel->name}}" class="d-none">
            <div class="dates">
                <x-mfw::select name="hotel_{{ $hotel->id }}_dates"
                               :values="collect(array_keys($availability_recap))->mapWithKeys(fn($item) => [$item => \Carbon\Carbon::createFromFormat('Y-m-d', $item)->format('d/m/Y')])->toArray()"/>
            </div>
            <div class="stocks">
                @forelse($availability_recap as $date => $subset)
                    @foreach($subset as $roomgroup_id => $total)
                        <div
                            class="room-group-{{ $roomgroup_id }} date-{{ $date }}"
                            data-name="{{ $availability_roomgroups[$roomgroup_id]['name'] }}"
                            data-room-group="{{ $roomgroup_id }}"
                            data-total="{{ $total }}"></div>
                    @endforeach
                @empty
                @endforelse
            </div>
        </div>
    @endforeach
</div>

@push('js')
    <script src="{{ asset('js/eventmanager/blocked_group_rooms.js') }}"></script>
@endpush
