<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum InterventionParticipantRole: string implements BackedEnumInteface
{

    case ORATOR = 'orator';
    case PARTICIPANT = 'participant';

    use BackedEnum;

    public static function default(): string
    {
        return self::PARTICIPANT->value;
    }

}
