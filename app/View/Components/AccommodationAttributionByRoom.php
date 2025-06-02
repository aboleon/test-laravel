<?php

namespace App\View\Components;

use App\Models\Order\Cart\AccommodationCart;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;


class AccommodationAttributionByRoom extends Component
{

    public Collection $attributions;
    /**
     * Create a new component instance.
     */
    public function __construct(public AccommodationCart $accommodationCart, public int $roomId)
    {
        $this->attributions = $this->accommodationCart->order->accommodationAttributions->isNotEmpty() ? $this->accommodationCart->order->accommodationAttributions->filter(fn($item) => $item->shoppable_id == $this->roomId) : collect();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accommodation-attribution-by-room');
    }
}
