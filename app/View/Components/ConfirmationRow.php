<?php

namespace App\View\Components;


use App\Interfaces\Stockable;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use MetaFramework\Accessors\Prices;

class ConfirmationRow extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public Stockable $stockable,
        public ?Collection $services)
    {

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.confirmation-row');
    }

    public function vat(): string
    {
        $value = $this->stockable->total_pec ? $this->stockable->vat->rate : $this->stockable->vat->rate;
        $value.= ' %';
        return $value;
    }
    public function ttc(): string
    {
        return Prices::readableFormat($this->stockable->total_pec ?? $this->stockable->total_net + $this->stockable->total_vat);
    }

    public function unitPrice(): string
    {
        return Prices::readableFormat($this->stockable->unit_price);
    }
}
