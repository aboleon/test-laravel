<?php

namespace App\View\Components;

use App\Models\DictionnaryEntry;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Models\Order\Cart\ServiceCart;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ConfirmationGroupServiceRow extends Component
{

    public ?DictionnaryEntry $group;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public Sellable $sellable,
        public EventContact $eventContact,
    )
    {
        $this->group = $this->sellable?->event->services->where('id', $this->sellable->service_group)->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.confirmation-group-service-row');
    }
}
