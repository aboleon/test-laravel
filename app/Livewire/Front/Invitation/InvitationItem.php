<?php

namespace App\Livewire\Front\Invitation;

use App\Enum\EventProgramParticipantStatus;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Sellable\Choosable;
use App\Models\EventManager\Sellable\EventContactSellableServiceChoosable;
use Livewire\Component;

class InvitationItem extends Component
{

    public Event $event;
    public EventContact $eventContact;
    public Choosable $item;
    public bool $quantityAccepted = false;


    public function render()
    {
        return view('livewire.front.invitation.invitation-item');
    }

    public function accept()
    {
        $this->updateStatus(EventProgramParticipantStatus::VALIDATED->value);
    }

    public function deny()
    {
        $this->updateStatus(EventProgramParticipantStatus::DENIED->value);
    }

    public function updateQuantityAccepted($value)
    {
        $quantityAccepted = (bool)$value;
        $this->quantityAccepted = $quantityAccepted;

        $ecChoosable = EventContactSellableServiceChoosable::where('event_contact_id', $this->eventContact->id)
            ->where('choosable_id', $this->item->id)
            ->first();
        $ecChoosable?->update(['invitation_quantity_accepted' => $quantityAccepted ? 1 : null]);
        // TODO : il y avait ici une incré / décrémentation du stock en fonction de l'acceptation
        // A voir s'il faut pas le transformer en Cart
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function updateStatus(string $status)
    {
        $previousStatus = null;
        $ecChoosable = EventContactSellableServiceChoosable::where('event_contact_id', $this->eventContact->id)
            ->where('choosable_id', $this->item->id)
            ->first();
        $previousStatus = $ecChoosable?->status;


        if ($previousStatus !== $status) {
            EventContactSellableServiceChoosable::updateOrCreate(
                [
                    'event_contact_id' => $this->eventContact->id,
                    'choosable_id' => $this->item->id
                ],
                ['status' => $status],
            );

            // TODO : il y avait ici une décré / incrémentation du stock
        }
    }
}
