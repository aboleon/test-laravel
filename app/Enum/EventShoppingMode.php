<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum EventShoppingMode: string implements BackedEnumInteface
{

    case FIXED = 'fixed';
    case CUSTOM = 'custom';

    use BackedEnum;

    public static function default(): string
    {
        return self::FIXED->value;
    }

}
