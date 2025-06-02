<?php

namespace App\View\Components;

use App\Models\Order\Cart\AccommodationCart;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class AccompanyingNoteByRoom extends Component
{
    public Collection $accompanying_notes;
    /**
     * Create a new component instance.
     */
    public function __construct(public AccommodationCart $accommodationCart, public int $roomId)
    {
        $this->accompanying_notes = $this->accommodationCart->order->accompanying->isNotEmpty() ? $this->accommodationCart->order->accompanying->filter(fn($item) => $item->room_id == $this->roomId) : collect();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accompanying-note-by-room');
    }
}
