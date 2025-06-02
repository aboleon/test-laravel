<?php

namespace App\Listeners;

use App\Accessors\EventContactAccessor;
use App\Actions\EventManager\GrantActions;
use App\Events\ContactSaved;
use Throwable;

class AccountPecEligibility
{
    public function handle(ContactSaved $event): void
    {
        $account = $event->account;
        $event   = $event->event;

        try {
            $eventContact = EventContactAccessor::getEventContactByEventAndUser($event, $account);
            (new GrantActions())->updateEligibleStatusForSingleContact($event, $eventContact);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
