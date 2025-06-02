<?php

namespace App\Traits\Models;

use App\Models\EventManager\Transport\EventTransport;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait EventTransportModelTrait
{
    use Responses;
    use ValidationModelPropertiesTrait;

    protected ?EventTransport $eventTransport= null;

    public function setEventTransport(null|int|EventTransport $model): self
    {
        $this->eventTransport = is_int($model) ? EventTransport::find($model) : $model;

        return $this;
    }

    public function getEventTransport(): ?EventTransport
    {
        return $this->eventTransport;
    }

}
