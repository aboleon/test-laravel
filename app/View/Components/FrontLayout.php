<?php

namespace App\View\Components;

use App\Accessors\Front\FrontCache;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class FrontLayout extends Component
{
    public function render(): Renderable
    {
        return view('layouts.front', [
            'isGroupManager' => FrontCache::isConnectedAsGroupManager(),
        ]);
    }
}
