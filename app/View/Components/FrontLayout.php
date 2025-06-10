<?php

namespace App\View\Components;

use App\Accessors\Front\FrontCache;
use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class FrontLayout extends Component
{
    public function __construct(public Event $event)
    {

    }
    public function render(): Renderable
    {
        return view('layouts.front', [
            'isGroupManager' => FrontCache::isConnectedAsGroupManager(),
        ]);
    }
}
