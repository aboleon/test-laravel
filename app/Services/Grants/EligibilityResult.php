<?php

namespace App\Services\Grants;

class EligibilityResult
{
    private bool $age;
    private bool $domains;
    private bool $professions;
    private bool $participations;
    private bool $establishments;
    private bool $locations;
    private array $matches;
    private int $grantId;
    private int|float $score = 0;
    public static array $coefficients = [
        'domains' => 1,
        'participations' => 1,
        'professions' => 1,
        'establishments' => 1,
        'locations' => [
            'locality' => 1,
            'country' => 0.6,
            'continent' => 0.3,
        ]
    ];

    public function __construct(int $grantId)
    {
        $this->age = false;
        $this->domains = false;
        $this->professions = false;
        $this->participations = false;
        $this->establishments = false;
        $this->locations = false;
        $this->matches = [];
        $this->grantId = $grantId;
    }

    public function setAge(bool $value): void
    {
        $this->age = $value;
    }

    public function setDomains(bool $value): void
    {
        $this->domains = $value;
    }

    public function setProfessions(bool $value): void
    {
        $this->professions = $value;
    }

    public function setParticipations(bool $value): void
    {
        $this->participations = $value;
    }

    public function setEstablishments(bool $value): void
    {
        $this->establishments = $value;
    }

    public function setLocations(bool $value): void
    {
        $this->locations = $value;
    }

    public function addMatch(string $type, string $value, bool $quota): void
    {
        $this->matches[] = [
            'quota' => (int)$quota,
            'type' => $type,
            'value' => $value
        ];
    }

    public function getAge(): bool
    {
        return $this->age;
    }

    public function getDomains(): bool
    {
        return $this->domains;
    }

    public function getProfessions(): bool
    {
        return $this->professions;
    }

    public function getParticipations(): bool
    {
        return $this->participations;
    }

    public function getEstablishments(): bool
    {
        return $this->establishments;
    }

    public function getLocations(): bool
    {
        return $this->locations;
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function mergeMatches(array $matches): void
    {
        $this->matches = array_merge($this->matches, $matches);
    }

    public function getGrantId(): int
    {
        return $this->grantId;
    }

    public function toArray(): array
    {
        return [
            'grant_id' => $this->grantId,
            'eligibility' => [
                'age' => $this->age,
                'domains' => $this->domains,
                'professions' => $this->professions,
                'participations' => $this->participations,
                'establishments' => $this->establishments,
                'locations' => $this->locations
            ],
            'matches' => $this->matches
        ];
    }

    public function setScore() :void
    {
        $scoring = array_filter($this->getMatches(), fn($item) => $item['quota'] > 0);
        foreach ($scoring as $item) {
            if ($item['type'] == 'locations') {
                $this->score += self::$coefficients['locations'][$item['geo_type']];
                continue;
            }
            $this->score += self::$coefficients[$item['type']];
        }
    }

    public function getScore(): int
    {
        return $this->score;
    }
}
