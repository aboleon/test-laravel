<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventContactsSecondaryFilter extends Component
{
    public string $route = '';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $eventId,
        public string $group = 'all',
        public ?string $secondaryFilter = null
    )
    {
        $this->route = route('panel.manager.event.event_contact.index', ['event' => $this->eventId, 'group' => $this->group]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.event-contacts-secondary-filter')->with(['route' => $this->route]);
    }
}
