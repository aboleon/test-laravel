<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum OrderAmendedType: string implements BackedEnumInteface
{

    case CART = 'cart';
    case ORDER = 'order';

    use BackedEnum;

    public static function default(): string
    {
        return self::CART->value;
    }

}
