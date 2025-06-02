<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderSaved
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Order $order,
        public bool  $created = false
    )
    {
        $this->order->load('event.texts');
    }
}
