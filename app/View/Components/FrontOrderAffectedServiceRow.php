<?php

namespace App\View\Components;

use App\DataTables\View\EventGroupContactView;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Str;

class FrontOrderAffectedServiceRow extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $attributions ,
        public EventGroupContactView $member,
        public array              $services = []
    )
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.front-order-affected-service-row');
    }
}
