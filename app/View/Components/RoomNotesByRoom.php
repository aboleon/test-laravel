<?php

namespace App\View\Components;

use App\Models\Order\Cart\AccommodationCart;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class RoomNotesByRoom extends Component
{
    public Collection $room_notes;
    /**
     * Create a new component instance.
     */
    public function __construct(public AccommodationCart $accommodationCart, public int $roomId)
    {
        $this->room_notes = $accommodationCart->order->roomnotes->isNotEmpty() ? $accommodationCart->order->roomnotes->filter(fn($item) => $item->room_id == $this->roomId) : collect();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.room-notes-by-room');
    }
}
