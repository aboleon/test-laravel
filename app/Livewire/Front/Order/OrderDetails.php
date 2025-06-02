<?php

namespace App\Livewire\Front\Order;

use App\Accessors\EventAccessor;
use App\Accessors\OrderAccessor;
use App\Models\Order;
use Illuminate\Contracts\Support\Renderable;
use Livewire\Component;

class OrderDetails extends Component
{

    public Order $order;
    public $canAlterOrder = true;


    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function getOrderAccessorProperty(): OrderAccessor
    {
        return new OrderAccessor($this->order);
    }

    public function getEventAccessorProperty(): EventAccessor
    {
        return new EventAccessor($this->order->event);
    }

    public function disableOrderAlteration(): void
    {
        $this->canAlterOrder = false;
    }

    public function enableOrderAlteration(): void
    {
        $this->canAlterOrder = true;
    }

    public function getCanAlterOrderProperty(): bool
    {
        return $this->canAlterOrder;
    }

    public function render(): Renderable
    {
        return view('livewire.front.order.order-details');
    }
}
