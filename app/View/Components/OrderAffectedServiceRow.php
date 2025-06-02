<?php

namespace App\View\Components;

use App\Models\Order\Cart\ServiceAttribution;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Str;

class OrderAffectedServiceRow extends Component
{
    public string $identifier;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ServiceAttribution $attribution,
        public array              $services = []
    )
    {
        $this->identifier = $this->attribution->id ? 'service-'.$attribution->shoppable_id . ' member-'.$this->attribution->event_contact_id : Str::random(8);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.order-affected-service-row');
    }
}
