<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum SavedSearches: string implements BackedEnumInteface
{

    case CONTACTS = 'contacts';

    case EVENT_CONTACTS = 'event_contacts';

    case GROUPS = 'groups';

    use BackedEnum;

    public static function default(): string
    {
        return self::CONTACTS->value;
    }

}
