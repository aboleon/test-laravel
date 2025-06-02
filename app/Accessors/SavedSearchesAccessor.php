<?php

namespace App\Accessors;

use App\Models\SavedSearch;

class SavedSearchesAccessor
{
    public static function getIdToNameArray($type): array
    {
        return SavedSearch::where('type', $type)->pluck('name', 'id')->toArray();
    }
}
