<?php

namespace App\Enum;

use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum AmountType: string implements BackedEnumInteface
{
    case NET = 'ht';
    case TAX = 'ttc';

    use BackedEnum;

    public static function default(): string
    {
        return self::TAX->value;
    }
}
