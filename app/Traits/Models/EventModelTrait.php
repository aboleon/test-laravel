<?php

namespace App\Traits\Models;

use App\Models\Event;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait EventModelTrait
{
    use Responses;
    use ValidationModelPropertiesTrait;

    protected ?Event $event = null;
    private ?int $eventId = null;

    // Event Object
    public function setEvent(null|int|Event $event): self
    {
        $this->event = is_int($event) ? Event::find($event) : $event;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    // Simple Event ID
    public function setEventId(?int $eventId): self
    {
        $this->eventId = $eventId;
        return $this;
    }

    public function getEventId(): ?int
    {
        if ($this->eventId !== null) {
            return $this->eventId;
        }

        return $this->event?->id;
    }



}
