<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;


enum ParticipantType: string implements BackedEnumInteface
{

    case CONGRESS = 'congress';
    case INDUSTRY = 'industry';

    case ORATOR = 'orator';

    use BackedEnum;

    public static function default(): string
    {
        return self::CONGRESS->value;
    }

}
