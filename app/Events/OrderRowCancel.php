<?php

namespace App\Events;

use App\Interfaces\Stockable;
use App\Models\Order;
use App\Models\Order\Cart\ServiceCart;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRowCancel
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Order $order,
        public ?Stockable $stockable = null,
    )
    {
        $this->order->load('event.texts');
    }
}
