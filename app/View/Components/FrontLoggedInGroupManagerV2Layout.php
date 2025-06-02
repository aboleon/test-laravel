<?php

namespace App\View\Components;

use App\Accessors\EventManager\EventGroups;
use App\Accessors\Front\FrontCache;
use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class FrontLoggedInGroupManagerV2Layout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     */
    public function render(): Renderable
    {
        return view('layouts.front-logged-in-group-manager-v2');
    }
}
