<?php

namespace App\Services\Grants;

use App\Accessors\Accounts;
use App\Accessors\Dictionnaries;
use App\Models\AccountProfile;
use App\Models\EventContact;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Str;
use MetaFramework\Traits\Responses;

class AccountToGrantEligibility
{
    use Responses;

    private ?AccountProfile $profile;
    private readonly EloquentCollection $addresses;
    protected EligibilityResult $eligibility;
    private bool $grantQuota;
    private int $distributed;
    private int|float $score = 0;
    private GeoEligibility $geoChecker;


    public function __construct(public EventContact $contact, public ParsedGrant $grant)
    {
        $this->profile = $this->contact?->profile;
        $this->addresses = $this->contact->address;
        $this->grantQuota = $this->grant['quota']->isNotEmpty();
        $this->distributed = $this->grant['budget']['pax_financed'];
        $this->geoChecker = new GeoEligibility($this->addresses, $this->grant, $this->grantQuota, $this->distributed);
        $this->eligibility = new EligibilityResult($this->grant['id']);
    }

    private function isAccountNull(): bool
    {
        if (is_null($this->profile)) {
            $this->responseError("Le contact n'as pas de profil.");
            return false;
        }
        return true;
    }

    public function calculate(): self
    {
        $this->isAccountNull();

        if ($this->hasErrors()) {
            return $this;
        }

        $this->eligibility->setAge($this->isAgeEligible());
        $this->eligibility->setDomains($this->isDomainEligible());
        $this->eligibility->setProfessions($this->isProfessionEligible());
        $this->eligibility->setParticipations($this->isParticipationEligible());
        $this->eligibility->setEstablishments($this->isEstablishmentEligible());
        $this->eligibility->setLocations($this->geoChecker->isGeoEligible());

        $this->eligibility->mergeMatches($this->geoChecker->getMatches());
        $this->eligibility->setScore();

        return $this;
    }

    public function getEligibility(): EligibilityResult
    {
        return $this->eligibility;
    }

    public function getMatches(): array
    {
        return $this->eligibility->getMatches();
    }

    private function isAgeEligible(): bool
    {
        $minAge = $this->grant['age']['min'] ?? 0;
        $maxAge = $this->grant['age']['max'] ?? 0;

        if (!$minAge && !$maxAge) {
            return true;
        }

        $birthdate = $this->profile->birth ?? null;

        if (!$birthdate) {
            return false;
        }

        $age = $this->profile->birth->diffInYears(Carbon::now());

        if ($minAge && !$maxAge) {
            return $age >= $minAge;
        }

        if (!$minAge && $maxAge) {
            return $age <= $minAge;
        }

        return $age >= $minAge && $age <= $maxAge;
    }

    private function isDomainEligible(): bool
    {
        return $this->eligibilityParser('domains', $this->profile->domain_id);
    }

    private function isMedical(): bool
    {
        return !is_null($this->profile?->profession_id) && $this->profile?->account_type == 'medical' && array_key_exists($this->profile->profession_id, Dictionnaries::medicalProfessions());
    }
    public function hasAddressInFrance(): bool
    {
        if ($this->addresses->isEmpty()) {
            return false;
        }
        return (bool)$this->addresses->where('country_code', 'FR')->count();
    }

    private function isProfessionEligible(): bool
    {
        $account = $this->contact?->account;

        if ($account) {
            if ($this->isMedical() && $this->hasAddressInFrance()) {
                if (empty($this->profile->rpps)) {
                    return false;
                }
            }
        }

        return $this->eligibilityParser('professions', $this->profile->profession_id);
    }

    private function isEstablishmentEligible(): bool
    {
        return $this->eligibilityParser('establishments', $this->profile->establishment_id);
    }

    private function isParticipationEligible(): bool
    {
        return $this->eligibilityParser('participations', $this->contact->participation_type_id);
    }

    private function eligibilityParser(string $grantKey, ?string $accountValue): bool
    {
        if (!$this->grant[$grantKey]) {
            $this->eligibility->addMatch($grantKey, 'no_requirements', false);
            return true;
        }
        if ($accountValue && array_key_exists($accountValue, $this->grant[$grantKey])) {

            $pax_max = $this->grant[$grantKey][$accountValue];
            if (!$pax_max) { // no quota
                $this->eligibility->addMatch($grantKey, $accountValue, false);
                return true;
            } else {
                if (!$this->grantQuota) {
                    $this->eligibility->addMatch($grantKey, $accountValue, true);
                    return true;
                }
                $limitedQuota = $this->grant['quota']->where(['type' => $grantKey, 'value' => $accountValue])->count();
                if ($limitedQuota < $pax_max) {
                    $this->eligibility->addMatch($grantKey, $accountValue, true);
                    return true;
                }
            }
        }

        return false;
    }

    public function passes(): bool
    {
        return count(array_filter($this->eligibility->toArray()['eligibility'], fn($item) => $item !== true)) < 1;
    }

    public function getScore(): int
    {
        if (!$this->passes()) {
            return $this->score;
        }

        return $this->eligibility->getScore();
    }
}
