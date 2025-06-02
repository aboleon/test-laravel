<?php

namespace App\Livewire\Front\Intervention;

use App\Enum\EventProgramParticipantStatus;
use App\Models\Event;
use App\Models\EventManager\Program\EventProgramInterventionOrator;
use App\Models\EventManager\Program\EventProgramSessionModerator;
use Livewire\Component;

class ParticipantItem extends Component
{

    public EventProgramSessionModerator|EventProgramInterventionOrator $item;
    public Event $event;

    public bool $pdfAuthorization;
    public bool $videoAuthorization;

    public function mount(EventProgramSessionModerator|EventProgramInterventionOrator $item, Event $event)
    {
        $this->item = $item;
        $this->event = $event;
        $this->videoAuthorization = (bool)$item->allow_video_distribution;
        if ($item instanceof EventProgramInterventionOrator) {
            $this->pdfAuthorization = (bool)$item->allow_pdf_distribution;
        }
    }


    public function accept()
    {
        $this->item->status = EventProgramParticipantStatus::VALIDATED->value;
        $this->item->save();
    }

    public function deny()
    {
        $this->item->status = EventProgramParticipantStatus::DENIED->value;
        $this->item->save();
    }


    public function updatePdfAuthorization()
    {
        $this->item->allow_pdf_distribution = (int)$this->pdfAuthorization;
        $this->item->save();
    }

    public function updateVideoAuthorization()
    {
        $this->item->allow_video_distribution = (int)$this->videoAuthorization;
        $this->item->save();
    }


    public function render()
    {
        return view('livewire.front.intervention.participant-item');
    }
}
