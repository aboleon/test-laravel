<?php

namespace App\Services\Pec;

use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum PecType: string implements BackedEnumInteface
{
    use BackedEnum;

    case UNCATEGORIZED = 'uncategorized';
    case SERVICE = 'service';
    case ACCOMMODATION = 'accommodation';
    case TAXROOM = 'taxroom';
    case TRANSPORT = 'transport';
    case PROCESSING_FEE = 'processing_fee';

    public static function default(): string
    {
        return self::UNCATEGORIZED->value;
    }

}
