<?php

namespace App\Enum;

use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum SecondaryEventContactFilter: string implements BackedEnumInteface
{
    case ACCOMMODATION = 'accommodation';
    case NO_ACCOMMODATION = 'noAccommodation';
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case FRONTMADE = 'frontmade';
    case SERVICES_ONLY = 'servicesOnly';

    use BackedEnum;

    public static function default(): string
    {
        return self::ACCOMMODATION->value;
    }
}
