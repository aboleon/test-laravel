<?php

namespace App\Events;

use App\Models\EventContact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventContactPecUpdated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public EventContact $eventContact
    )
    {
        //
    }
}
