<?php

namespace App\View\Components;

use App\Accessors\Dictionnaries;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ParticipationTypes extends Component
{
    public Collection $participations;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public mixed $affected,
        public string $as = 'checkbox',
        public bool $all = false,
        public bool $filter = false,
        public array $subset = [],
        public bool $alltranslations = false
    )
    {
        $this->participations = Dictionnaries::participationTypes();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.participation-types');
    }
}
