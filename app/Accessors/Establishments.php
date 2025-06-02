<?php

namespace App\Accessors;

use App\Models\Establishment;
use Illuminate\Support\Facades\DB;
use MetaFramework\Accessors\Countries;

class Establishments
{
    /**
     * @return array<string>
     */
    public static function orderedIdNameArray(?string $expression = null): array
    {
        $b = Establishment::query();
        if ($expression) {
            $b->where('name', 'like', '%' . $expression . '%');
        }
        return $b->orderBy('name')->pluck('name', 'id')->toArray();
    }

    // Get the countries of the establishments for the registered accounts for the event
    public function representedEstablishmentCountries(): array
    {
        $countries      = Countries::orderedCodeNameArray();

        return collect(
            DB::table('establishments')
                ->distinct()
                ->pluck('country_code'),
        )
            ->filter()
            ->mapWithKeys(fn($code) => [$code => $countries[$code] ?? $code])
            ->toArray();
    }
}
