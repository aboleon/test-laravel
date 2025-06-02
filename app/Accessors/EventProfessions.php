<?php

namespace App\Accessors;

use App\Models\EventProfession;

class EventProfessions
{
    public static function getProfessionIdsByEventId(int $eventId): array
    {
        return EventProfession::where('event_id', $eventId)->pluck('profession_id')->toArray();
    }
}
