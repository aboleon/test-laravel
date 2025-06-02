<?php

namespace App\Traits\Models;

use App\Models\EventManager\Grant\Grant as EventGrant;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait EventGrantModelTrait
{
    use Responses;
    use ValidationModelPropertiesTrait;

    protected ?EventGrant $eventGrant= null;

    public function setEventGrant(null|int|EventGrant $model): self
    {
        $this->eventGrant = is_int($model) ? EventGrant::find($model) : $model;

        return $this;
    }

    public function getEventGrant(): ?EventGrant
    {
        return $this->eventGrant;
    }

}
