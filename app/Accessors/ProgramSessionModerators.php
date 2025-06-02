<?php

namespace App\Accessors;

use App\Models\EventContact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProgramSessionModerators
{


    public static function getModeratorsInfo(Collection $moderators, array $moderatorsInfo = [], ?int $sessionId = null): array
    {
        $moderatorsInfoArray = [];

        foreach ($moderators as $moderator) {
            $moderatorInfo = $moderatorsInfo[$moderator->id] ?? [];
            $moderatorsInfoArray[] = self::getModeratorInfo($moderator, $moderatorInfo, $sessionId);
        }
        return $moderatorsInfoArray;
    }

    public static function getModeratorInfo(EventContact $participant, array $moderatorInfo, ?int $sessionId = null): array
    {
        return ProgramParticipants::getModeratorOratorInfo('moderator', $participant, $moderatorInfo, $sessionId);
    }



}
