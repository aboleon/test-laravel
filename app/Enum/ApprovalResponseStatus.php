<?php

namespace App\Enum;

use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum ApprovalResponseStatus: string implements BackedEnumInteface
{

    use BackedEnum;

    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case DENIED = 'denied';


    public static function default(): string
    {
        return self::PENDING->value;
    }
}