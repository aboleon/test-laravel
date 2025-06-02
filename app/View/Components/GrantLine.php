<?php

namespace App\View\Components;

use App\Models\EventManager\Accommodation\Grant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class GrantLine extends Component
{
    public ?string $identifier;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public Grant $row,
        public Collection $dates,
        public array $roomgroups,
        public int $available = 0,
        public int $iteration = 0,
    ) {
        $this->identifier = $this->row->id ? 'grant-'.$this->row->id : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.grant-line');
    }
}
