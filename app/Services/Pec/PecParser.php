<?php

namespace App\Services\Pec;

use App\Models\Event;
use App\Models\EventContact;
use App\Services\Grants\AccountToGrantEligibility;
use App\Services\Grants\EligibilityResult;
use App\Services\Grants\GrantParser;
use App\Services\Grants\ParsedGrant;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use MetaFramework\Traits\Responses;

class PecParser
{
    use Responses;

    public readonly GrantParser $grantParser;

    protected array $params;
    protected Collection $eligibleGrants;
    public Collection $availableGrants;
    private Collection $contactEligibility;

    private bool $trackFailures = false;
    private Collection $eligibilityFailure;

    public function __construct(public Event $event, public Collection|EloquentCollection $contacts)
    {
        $this->eligibleGrants = collect();
        $this->availableGrants = collect();
        $this->contactEligibility = collect();
        $this->eligibilityFailure = collect();
    }

    public function calculate(): void
    {
        $this->initializeGrantParser();

        if ($this->grantParser->hasErrors()) {
            $this->handleGrantParserErrors();
            return;
        }

        $this->processAvailableGrants();
    }

    public function trackFailures(): self
    {
        $this->trackFailures = true;
        return $this;
    }

    private function initializeGrantParser(): void
    {
        $this->grantParser = new GrantParser($this->event);
    }

    private function handleGrantParserErrors(): void
    {
        $this->pushMessages($this->grantParser);
    }

    private function processAvailableGrants(): void
    {
        $this->availableGrants = $this->grantParser->fetchAvailableGrants();

        if ($this->availableGrants->isEmpty()) {
            $this->responseError("Aucun grant disponible.");
            return;
        }

        foreach ($this->availableGrants as $grant) {
            foreach ($this->contacts as $contact) {
                $this->processGrant($contact, $grant);
            }
        }

        $this->eligibleGrants = $this->eligibleGrants->sortByDesc('score');
    }

    public function processGrant(EventContact $contact, $grant): void
    {
        $eligible = new AccountToGrantEligibility($contact, $grant);
        $eligible->calculate();

        if ($eligible->passes()) {

            if (!$this->contactEligibility->has($contact->id)) {
                $this->contactEligibility->put($contact->id, collect());
            }

            $this->contactEligibility[$contact->id]->push(
                $eligible->getEligibility()
            );

            $grant->setScore($eligible->getScore());
            if (!$this->eligibleGrants->has($grant->id)) {
                $this->eligibleGrants->put($grant->id, $grant);
            }

        } else {
            if ($this->trackFailures) {
                $this->eligibilityFailure->put($contact->id, $eligible->getEligibility());
            }
        }
    }

    public function getEligibilityFailures(): Collection
    {
        return $this->eligibilityFailure;
    }
    public function getEligibleGrants(): Collection
    {
        return $this->eligibleGrants;
    }

    public function hasGrants(int $contact_id): bool
    {
        return $this->contactEligibility->has($contact_id);
    }

    public function getGrantsFor(int|EventContact $contact): Collection
    {
        $id = is_int($contact) ? $contact : $contact->id;

        if ($this->hasGrants($id)) {
            return $this->eligibleGrants
                ->filter(fn($item, $key) => $this->contactEligibility[$id]->map(fn($eligilityResult) => $eligilityResult->getGrantId() == $key))
                ->sortByDesc('score');
        }
        return collect();
    }

    public function getPreferedGrantFor(int|EventContact $contact): ?ParsedGrant
    {
        $id = is_int($contact) ? $contact : $contact->id;

        return $this->getGrantsFor($id)->first();
    }

    public function getEligibilitySummary(): Collection
    {
        return $this->contactEligibility;
    }

    public function getEligibilityFor(int|EventContact $contact, int $grant_id): ?EligibilityResult
    {
        $id = is_int($contact) ? $contact : $contact->id;

        if (!$this->contactEligibility->has($id)) {
            $this->responseError("Aucune éligibilité pour le contact ID ". $id);
            return null;
        }

        return $this->contactEligibility[$id]->filter(fn($item) => $item->getGrantId() == $grant_id)->first();
    }

    public function getContactIds(): Collection
    {
        return $this->contacts->pluck('id');
    }

    public function getEligibileContactIds(): Collection
    {
        return $this->contactEligibility->keys();
    }

    public function getNonEligibleContactIds(): Collection
    {
        return $this->getContactIds()->diff($this->getEligibileContactIds());
    }
}
