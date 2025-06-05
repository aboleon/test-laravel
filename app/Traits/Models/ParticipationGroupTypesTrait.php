<?php

namespace App\Traits\Models;

use App\Enum\ParticipantType;
use App\Models\Event;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait ParticipationGroupTypesTrait
{

    protected array $participationGroupTypes = [];

    public function setGroups(): static
    {
        $this->participationGroupTypes =ParticipantType::values();

        return $this;
    }

    public function getGroups(): array
    {
        return $this->participationGroupTypes;
    }

    public function getGroupsAsString(): string
    {
        return "'" . implode("', '", $this->getGroups()) . "'";
    }


    public function unsetGroup(string $group): static
    {
        $this->participationGroupTypes = array_values(array_diff($this->participationGroupTypes, [$group]));

        return $this;
    }


}
