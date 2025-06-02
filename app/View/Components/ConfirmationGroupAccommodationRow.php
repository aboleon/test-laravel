<?php

namespace App\View\Components;

use App\Models\EventContact;
use App\Models\Order\Attribution;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class ConfirmationGroupAccommodationRow extends Component
{
    public string $printableDate = '';
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Attribution $attribution,
        public EventContact $eventContact,
        public array $hotels = [],
    )
    {
        $this->printableDate = isset($attribution->configs['date']) ? Carbon::parse($attribution->configs['date'])->format('d/m/Y') : '';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.confirmation-group-accommodation-row');
    }
}
