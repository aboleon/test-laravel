<?php

namespace App\View\Components;

use App\Models\EventShoppingRanges;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShopRangeFee extends Component
{
    /**
     * NOTICE :
     * ShopRange Fee
     */
    /**
     * Create a new component instance.
     */
    public function __construct(public EventShoppingRanges $range)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.shop-range-fee');
    }
}
