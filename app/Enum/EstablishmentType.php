<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum EstablishmentType: string implements BackedEnumInteface
{

    case PRIVATE = 'private';
    case PUBLIC = 'public';

    use BackedEnum;

    public static function default(): string
    {
        return self::PRIVATE->value;
    }
}
