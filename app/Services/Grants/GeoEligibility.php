<?php

namespace App\Services\Grants;

use App\Accessors\Countries;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GeoEligibility
{
    private array $match = [];

    const string LOCALITY = 'locality';
    const string COUNTRY = 'country';
    const string CONTINENT = 'continent';

    public function __construct(
        private readonly EloquentCollection $addresses,
        private readonly ParsedGrant        $grant,
        private readonly bool               $grantQuota,
        private readonly int                $distributed
    )
    {
    }

    public function isGeoEligible(): bool
    {
        if (!$this->grant['locations']) {
            $this->setLocationMatch('none', 'no_requirements', false);
            return true;
        }

        if ($this->addresses->isEmpty()) {
            return false;
        }

        $collection = collect($this->grant['locations'])->groupBy('type');
        $userGeo = $this->addresses->mapWithKeys(fn($item) => [Str::slug($item->locality) => $item->country_code]);

        if ($this->hasLocalityMatch($collection, $userGeo)) {
            return true;
        }

        $userCountries = $this->addresses->pluck('country_code')->unique()->toArray();


        if ($this->matchCountries($collection, $userCountries)) {
            return true;
        }

        return $this->matchContinents($collection, $userCountries);
    }

    private function hasLocalityMatch(Collection $collection, Collection $userGeo): bool
    {
        if (!isset($collection[self::LOCALITY])) {
            return false;
        }

        $matches = $collection[self::LOCALITY]->filter(function ($item) use ($userGeo) {
            $slug = Str::slug($item[self::LOCALITY]);
            return $userGeo->has($slug) && $userGeo[$slug] === $item['country_code'];
        });

        if ($matches->isEmpty()) {
            return false;
        }

        $userCountries = $matches->pluck(self::COUNTRY)->unique()->toArray();
        $limitedQuota = $matches->filter(fn($item) => $item['pax'] > 0 && $this->distributed < $item['pax']);

        if ($this->grantQuota && $limitedQuota->isNotEmpty()) {
            return $this->checkLimitedQuota($matches, self::LOCALITY, $userCountries);
        }

        $defaultMatch = $matches->first();
        $this->setLocationMatch(self::LOCALITY, $defaultMatch[self::LOCALITY] . '-' . $defaultMatch['country_code'], (bool)$defaultMatch['pax']);
        return true;

    }

    private function matchCountries(Collection $collection, array $userCountries): bool
    {
        if (!isset($collection[self::COUNTRY])) {
            return false;
        }

        $matchingCountries = $collection[self::COUNTRY]->filter(fn($item) => in_array($item['country_code'], $userCountries));

        if ($matchingCountries->isEmpty()) {
            return false;
        }

        $limitedQuota = $this->getQuotaMatches($matchingCountries);

        if ($this->grantQuota && $limitedQuota->isNotEmpty()) {
             return $this->checkLimitedQuota($matchingCountries, self::COUNTRY, $userCountries);
        }
        $defaultMatch = $matchingCountries->first();
        $this->setLocationMatch(self::COUNTRY, $defaultMatch['country_code'], (bool)$defaultMatch['pax']);
        return true;
    }

    private function matchContinents(Collection $collection, array $userCountries): bool
    {
        if (!isset($collection[self::CONTINENT])) {
            return false;
        }

        $continents = collect($userCountries)->map(fn($item) => Countries::getContinentCode($item));

        $matchingContinents = $collection[self::CONTINENT]->filter(fn($item) => $continents->contains($item[self::CONTINENT]));
        $limitedQuota = $this->getQuotaMatches($matchingContinents);

        if ($matchingContinents->isEmpty()) {
            return false;
        }

        if ($this->grantQuota && $limitedQuota->isNotEmpty()) {
            return $this->checkLimitedQuota($matchingContinents, self::CONTINENT, $userCountries);
        }

        $defaultMatch = $matchingContinents->first();
        $this->setLocationMatch(self::CONTINENT, $defaultMatch[self::CONTINENT], (bool)$defaultMatch['pax']);
        return true;

    }

    private function getQuotaMatches(Collection $collection): Collection
    {
        return $collection->filter(fn($item) => $item['pax'] > 0 && $this->distributed < $item['pax']);
    }

    private function checkLimitedQuota(Collection $matches, string $geoType, array $userCountries): bool
    {
        foreach ($matches as $item) {
            $filledQuota = $this->grant['quota']->where(['type' => 'geo', 'geo_type' => $geoType, 'value' => $item[self::LOCALITY] . '-' . $item['country_code']])->count();

            if ($filledQuota < $item['pax']) {
                $this->setLocationMatch($geoType, $item[self::LOCALITY] . '-' . $item['country_code'], true);
                return true;
            }
        }

        return $this->matchCountries($matches, $userCountries);
    }

    private function setLocationMatch(string $subtype, string $value, bool $quota): void
    {
        $this->match[] = [
            'quota' => (int)$quota,
            'type' => 'locations',
            'value' => $value,
            'geo_type' => $subtype,
        ];
    }

    public function getMatches(): array
    {
        return $this->match;
    }
}
