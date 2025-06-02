<?php

namespace App\Traits\Models;

use App\Models\Account;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\User;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait EventContactModelTrait
{
    use Responses;
    use ValidationModelPropertiesTrait;

    protected ?EventContact $eventContact = null;

    public function setEventContactFromEventAccount(int|Event $event, int|Account $account): self
    {
        $event_id = $event instanceof Event ? $event->id : $event;
        $user_id  = $account instanceof Account ? $account->id : $account;

        $this->eventContact = EventContact::where([
            'user_id'  => $user_id,
            'event_id' => $event_id,
        ])->first();

        return $this;
    }

    public static function getEventContactByEventAndUser(int|Event $eventId, int|User|Account $userId): ?EventContact
    {
        $static = new static();
        $static->setEventContactFromEventAccount($eventId, $userId);

        return $static->getEventContact();
    }

    public function setEventContact(int|EventContact $eventContact): self
    {
        if (is_null($this->eventContact)) {
            $this->eventContact = is_int($eventContact) ? EventContact::find($eventContact) : $eventContact;
        }

        return $this;
    }

    public function getEventContact(): ?EventContact
    {
        return $this->eventContact;
    }

}
