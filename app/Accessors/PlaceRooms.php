<?php

namespace App\Accessors;

use App\Models\PlaceRoom;

class PlaceRooms
{
    public static function selectableArray($placeId = null): array
    {

        if (null === $placeId) {
            $query = PlaceRoom::all();
        } else {
            $query = PlaceRoom::where('place_id', $placeId);
        }
        return $query->pluck('name', 'id')->toArray();
    }
}
