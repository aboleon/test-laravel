<?php

namespace App\View\Components;

use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class FrontLoggedInLayout extends Component
{


    public function __construct(
        public ?Event $event = null,
        public bool $groupView = false,
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
        return view('layouts.front-logged-in', [
            'event' => $this->event,
            'groupView' => $this->groupView,
            'enableOrderBtn' => $this->enableOrderBtn,
        ]);
    }
}
