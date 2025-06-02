<?php

namespace App\Livewire\Front\Transport\Participant;

use App\Models\Event;
use App\Models\EventManager\Transport\EventTransport;
use Livewire\Component;

class TransportParticipantStepDocuments extends Component
{
    public EventTransport $transport;
    public Event $event;
    public bool $disable_title = false;
    public bool $final_submit = false;
    public bool $standalone_submit = false;
    public $transportParticipantDocuments = [];


    public function submit()
    {

    }

    public function render()
    {
        return view('livewire.front.transport.participant.transport-participant-step-documents');
    }


}
