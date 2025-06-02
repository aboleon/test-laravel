<?php

namespace App\Listeners;

use App\Actions\EventManager\GrantActions;
use App\Events\AccountSaved;
use App\Models\Event;
use App\Models\EventContact;
use MetaFramework\Traits\Responses;
use Throwable;

class AccountGlobalPecEligibility
{
    use Responses;

    public function handle(AccountSaved $event): void
    {
        $account = $event->account;


        try {
            // Update PEC for upcomings Events where the account is present

            $eventContacts = EventContact::where('user_id', $account->id)->whereIn('event_id', Event::whereRaw('DATE(ends) > CURDATE()')->pluck(('id')))->with('event')->get();

            foreach ($eventContacts as $eventContact) {
                (new GrantActions())->updateEligibleStatusForSingleContact($eventContact->event, $eventContact);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
