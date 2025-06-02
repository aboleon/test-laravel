<?php

namespace App\View\Components;

use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class FrontLoggedInGroupManagerLayout extends Component
{


    public function __construct(
        public ?Event $event = null,
        public bool $groupView = true,
        public bool $enableOrderBtn = true
    )
    {
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): Renderable
    {
        return view('layouts.front-logged-in-group-manager', [
            'event' => $this->event,
            'groupView' => $this->groupView,
            'enableOrderBtn' => $this->enableOrderBtn,
        ]);
    }
}
