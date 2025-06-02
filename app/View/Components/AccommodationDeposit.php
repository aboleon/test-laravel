<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use stdClass;

class AccommodationDeposit extends Component
{
    public int|string $id;
    public function __construct(public stdClass|\App\Models\EventManager\Accommodation\Deposit $deposit)
    {
        $this->id = $this->deposit->id ?? Str::random();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accommodation-deposit');
    }
}
