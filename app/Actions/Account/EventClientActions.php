<?php

namespace App\Actions\Account;

use App\Models\Account;
use App\Models\Event;
use App\Models\EventClient;
use App\Traits\Models\AccountModelTrait;
use App\Traits\Models\EventModelTrait;
use MetaFramework\Traits\Ajax;
use Throwable;

class EventClientActions
{
    use Ajax;
    use AccountModelTrait;
    use EventModelTrait;

    public function __construct(int|Account $account_id, int|Event $event_id)
    {
        try {
            $this->setAccount($account_id);
            $this->setEvent($event_id);
        } catch (Throwable $e) {
            $this->fetchInput();
            $this->responseException($e, "Le compte ou l'évènement ne peuvent pas être retrouvés à partir de ces identifiants");
        }
    }

    public function associateToEvent(): static
    {
        try {
            EventClient::firstOrCreate([
                'user_id' => $this->account->id,
                'event_id' => $this->event->id
            ]);

            $this->responseSuccess("Le client a été associé à l'évènement ". $this->event->texts->name .".");
            session()->flash('mfw_tab_redirect', 'contacts-tabpane-tab');
        } catch (Throwable $e) {
            $this->responseException($e);
            $this->responseException($e, "Une erreur est survenue à l'association du client à l'évènement.");
        }
        return $this;

    }

    public function dissociate(): static
    {
        try {
            EventClient::where([
                'user_id' => $this->account->id,
                'event_id' => $this->event->id
            ])->delete();
            $this->responseSuccess("Le client a été dissocié de l'évènement.");

        } catch (Throwable $e) {
            $this->responseException($e);
            $this->responseException($e, "Une erreur est survenue à la dissociation du client de l'évènement.");
        }
        return $this;
    }
}
