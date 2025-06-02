<?php

namespace App\Accessors;

use App\Models\Event;
use App\Models\ParticipationType;

/**
 * @property int      $id
 * @property bool|int $default
 * @property string   $name
 * @property string Enum\ParticipationType $group
 * @property string   $access_key
 */
class ParticipationTypes
{

    public static function selectable(?Event $event = null): array
    {
        if ($event) {
            return ParticipationType::query()
                ->join('event_participation', 'event_participation.participation_id', '=', 'participation_types.id')
                ->where('event_participation.event_id', $event->id)
                ->get()
                ->sortBy('name')
                ->groupBy('group')
                ->toArray();
        }

        return Dictionnaries::participationTypes()->toArray();
    }

    public static function getById(?int $id): ?ParticipationType
    {
        return $id ? Dictionnaries::participationTypes()->flatten()->filter(fn($item) => $item->id == $id)->first() : null;
    }

    public static function validKeys(): array
    {
        return Dictionnaries::participationTypes()->flatten()->pluck('id')->toArray();
    }

    public static function isValidId(int $id): bool
    {
        return in_array($id, self::validKeys(), true);
    }

    public static function default(): ?ParticipationType
    {
        return cache()->rememberForever('default_participation_type', function () {
            return Dictionnaries::participationTypes()->flatten()->filter(fn($item) => $item->default == 1)->first();
        });
    }

    public static function defaultId(): int
    {
        return (int)self::default()?->id;
    }
}
