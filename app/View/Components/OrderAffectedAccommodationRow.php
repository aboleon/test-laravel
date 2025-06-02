<?php

namespace App\View\Components;

use App\Models\EventManager\Accommodation\Room;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\Cart\AccommodationAttribution;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Str;

class OrderAffectedAccommodationRow extends Component
{
    public $room;
    public string $identifier;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public AccommodationAttribution $attribution,
        public AccommodationCart        $cart,
        public array                    $hotels,
        public Collection               $rooms,
    )
    {
        $this->room = $this->rooms->where('id', $this->attribution->shoppable_id)->first();

        $this->identifier = $this->attribution->id ? 'accommodation-'.$this->attribution->cart_id : '';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.order-affected-accommodation-row');
    }
}
