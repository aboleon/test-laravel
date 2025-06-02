<?php

namespace App\Livewire\Back\EventManager\Sellable;

use App\Models\Event;
use App\Models\EventManager\Sellable;
use App\Models\EventManager\Sellable\EventContactSellableServiceChoosable;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Livewire\Component;

class SellableInvitation extends Component
{
    public Sellable $sellable;
    public Event $event;
    public int $inscriptionId = 0;

    protected $listeners = ['refreshSellableInvitations' => '$refresh'];

    public function render(): Renderable
    {
        return view('livewire.back.eventManager.sellable.sellable-invitation');
    }


    /**
     * @throws Exception
     */
    public function deleteInscription()
    {
        $invitation = EventContactSellableServiceChoosable::find($this->inscriptionId);
        if ($invitation) {
            $invitation->delete();
            // TODO : il y avait ici une incrémentation du stock
            $this->dispatch('refreshSellableInvitations');
        } else {
            throw new Exception("Inscription non trouvée {$this->inscriptionId}");
        }
    }
}
