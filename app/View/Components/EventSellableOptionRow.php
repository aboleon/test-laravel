<?php

namespace App\View\Components;

use App\Models\EventManager\Sellable\Option;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class EventSellableOptionRow extends Component
{
    public string $identifier;
    public array $fillables;
    public string $random = 'random';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public Option $option
    )
    {
        $this->identifier = Str::random();
        $this->fillables = $option->fillables;
        if ($this->option->id) {
            $this->random = $this->identifier;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.event-sellable-option-row');
    }
}
