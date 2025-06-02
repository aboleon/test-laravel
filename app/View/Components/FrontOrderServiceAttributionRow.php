<?php

namespace App\View\Components;

use App\Models\DictionnaryEntry;
use App\Models\Event;
use App\Models\EventManager\Sellable;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class FrontOrderServiceAttributionRow extends Component
{
    public string $identifier;
    public int $qty = 1;
    public bool $multiple = false;
    public DictionnaryEntry $group;
    public Sellable $sellable;
    public string $locale;
    public bool $can_attribute = true;


    /**
     * Create a new component instance.
     */
    public function __construct(
        public \stdClass $item,
        public Event $event,
        public Collection $services,
    )
    {
        $this->identifier = Str::random();
        $this->locale = app()->getLocale();

        $this->sellable = $services->where('id', $item->service_id)->first();
        $this->group = $this->sellable->event->services->where('id', $this->sellable->service_group)->first();

        $this->can_attribute = $item->ordered > 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.front-order-service-attribution-row');
    }
}
