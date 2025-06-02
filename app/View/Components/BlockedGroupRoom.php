<?php

namespace App\View\Components;

use App\Models\Event;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class BlockedGroupRoom extends Component
{
    public ?string $identifier;
    public array $hotels;

    public function __construct(
        public Event                                            $event,
        public Collection                                       $accommodation,
        public \app\Models\EventManager\Groups\BlockedGroupRoom $row,
        public int                                              $iteration = 1
    )
    {
        $this->identifier = $this->row->id ? 'blocked-' . $this->row->id : null;
        $this->hotels = $this->accommodation->pluck('hotel.name', 'id')->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.blocked-group-room');
    }
}
