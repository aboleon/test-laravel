<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class PlaceRoomSetup extends Component
{
    public string $identifier;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public \App\Models\PlaceRoomSetup $setup,
        public int $loop = 0
    )
    {
        $this->identifier = Str::random();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.place-room-setup');
    }
}
