<?php

namespace App\Accessors\EventManager;

use App\Models\Event;
use App\Models\EventService;
use Illuminate\Database\Eloquent\Collection;

class EventServices
{
    public static function getEventService(Event $event, int $eventServiceId): EventService|null
    {
        return EventService::where('event_id', $event->id)->where('service_id', $eventServiceId)->first();
    }

    public static function getEventServiceByIds(Event $event, array $eventServiceIds): Collection
    {
        return EventService::where('event_id', $event->id)
            ->whereIn('service_id', $eventServiceIds)
            ->get();
    }
}