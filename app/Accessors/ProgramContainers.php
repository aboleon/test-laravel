<?php

namespace App\Accessors;

use App\Models\EventManager\Program\EventProgramDayRoom;

class ProgramContainers
{
    public static function getPlacesSelectable(int $eventId): array
    {
        return EventProgramDayRoom::with('room.place')
            ->where('event_id', $eventId)
            ->get()
            ->mapWithKeys(function ($dayRoom) {
                return [$dayRoom->room->place->id => $dayRoom->room->place->name];
            })
            ->toArray();
    }
}
