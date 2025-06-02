<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum OrderSource: string implements BackedEnumInteface
{

    case ORDER = 'order';
    case ATTRIBUTION = 'attribution';

    use BackedEnum;

    public static function default(): string
    {
        return self::ORDER->value;
    }

}
