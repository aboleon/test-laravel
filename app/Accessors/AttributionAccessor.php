<?php

namespace App\Accessors;

use Illuminate\Support\Collection;

class AttributionAccessor
{

    public static function accommodationSummary(Collection $collection)
    {
        return $collection->map(function ($entries, $date) {
            $totalSubsetCount        = count($entries); // Total number of items for this date
            $zeroQuantitySubsetCount = collect($entries)->filter(function ($entry) {
                return $entry->total_quantity == 0;
            })->count(); // Count where total_quantity is 0

            return [
                'date'                => $date,
                'total_count'         => $totalSubsetCount,
                'zero_quantity_count' => $zeroQuantitySubsetCount,
                'can_attribute'    => $totalSubsetCount > $zeroQuantitySubsetCount,
            ];
        })->toArray();
    }
}
