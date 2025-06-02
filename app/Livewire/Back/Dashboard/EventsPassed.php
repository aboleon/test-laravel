<?php

namespace App\Livewire\Back\Dashboard;

use App\Accessors\Dictionnaries;
use App\Accessors\EventAccessor;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class EventsPassed extends Component
{

    public Collection $passedEvents;
    public array $families;
    public string $search = '';

    public function render()
    {
        $this->updatePassedEvents();
        return view('livewire.back.dashboard.events-passed');
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function updatePassedEvents()
    {
        $this->families = Dictionnaries::selectValues('event_family');
        $this->passedEvents = EventAccessor::getPassedEvents($this->search);
    }
}
