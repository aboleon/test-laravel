<?php

namespace App\Livewire\Back\Dashboard;

use App\Accessors\Dictionnaries;
use App\Accessors\EventAccessor;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Events extends Component
{

    public Collection $upcomingEvents;
    public Collection $past2MonthsEvents;

    public array $families;
    public string $search = '';

    public function render()
    {
        $this->updateEvents();
        return view('livewire.back.dashboard.events');
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function updateEvents()
    {
        $this->families = Dictionnaries::selectValues('event_family');
        $events = EventAccessor::getDashboardEvents($this->search);
        $this->upcomingEvents = $events['upcomingEvents'];
        $this->past2MonthsEvents = $events['pastEvents'];
    }
}
