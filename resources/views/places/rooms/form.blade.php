<x-mfw::translatable-tabs :fillables="$data->fillables" :model="$data"/>

<h4>{{__('ui.rooms.complementary')}}</h4>

@php
    $setups = $data->setup;
        if ($errors->any()) {
            if (old('place_room_setup')) {
                $d = old('place_room_setup');
            $setups = collect();
                for($i=0;$i<count($d['name']);$i++) {
                    $setups->push(
                        new \App\Models\PlaceRoomSetup(
                            [
                                'name' => $d['name'][$i],
                                'capacity' => $d['capacity'][$i],
                                'description' => $d['description'][$i]
                            ]
                        )
                    );
                }
            }
        }
@endphp
<div id="place-room-setups">
    @foreach($setups as $setup)
        <x-place-room-setup :setup="$setup" :loop="$loop->index"/>
    @endforeach
</div>
<button class="btn btn-sm btn-success mt-3 d-inline-block" id="add-place-room-setup" type="button">
    <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
</button>

<x-optional-hidden string="place_id"/>
<x-optional-hidden string="selectable"/>


<div id="place_room_setup_messages" data-ajax="{{ route('ajax') }}"></div>
<template id="place-room-setup-row">
    <x-place-room-setup :setup="new \App\Models\PlaceRoomSetup()"/>
</template>
