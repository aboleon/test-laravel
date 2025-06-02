<?php

namespace App\Services\Grants;

use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum QuotaType: string implements BackedEnumInteface
{
    use BackedEnum;

    case DOMAIN = 'domains';
    case PROFESSION = 'professions';
    case PARTICIPATION = 'participations';
    case LOCATION = 'locations';
    case ESTABLISHMENT = 'establishments';

    public static function default(): string
    {
        return '';
    }

}
