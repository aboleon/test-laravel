<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum SellablePer: string implements BackedEnumInteface
{

    case UNIT = 'unit';
    case DAY = 'day';

    use BackedEnum;

    public static function default(): string
    {
        return self::UNIT->value;
    }
}
