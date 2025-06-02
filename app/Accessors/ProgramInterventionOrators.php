<?php

namespace App\Accessors;

use App\Models\EventContact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProgramInterventionOrators
{


    public static function getOratorsInfo(Collection $orators, array $oratorsInfo = [], ?int $interventionId = null): array
    {
        $oratorsInfoArray = [];

        foreach ($orators as $orator) {
            $oratorInfo = $oratorsInfo[$orator->id] ?? [];
            $oratorsInfoArray[] = self::getOratorInfo($orator, $oratorInfo, $interventionId);
        }
        return $oratorsInfoArray;
    }

    public static function getOratorInfo(EventContact $participant, array $oratorInfo, ?int $interventionId = null): array
    {
        return ProgramParticipants::getModeratorOratorInfo('orator', $participant, $oratorInfo, $interventionId);
    }
}
