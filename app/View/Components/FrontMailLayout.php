<?php

namespace App\View\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class FrontMailLayout extends Component
{
    public function render(): Renderable
    {
        return view('layouts.front-mail');
    }
}
