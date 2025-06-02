<?php

namespace App\View\Components;

use App\Accessors\Dictionnaries;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class BlockedRoom extends Component
{
    public ?string $identifier;
    public array $orators;

    public function __construct(
        public \App\Models\EventManager\Accommodation\BlockedRoom $row,
        public array                                              $roomgroups,
        public Collection                                         $dates,
        public array                                              $participationtypes = [],
        public int                                                $iteration = 0
    )
    {
        $this->identifier = $this->row->id ? 'blocked-' . $this->row->id : null;
        $this->orators = Dictionnaries::oratorsIds();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.blocked-room');
    }
}
