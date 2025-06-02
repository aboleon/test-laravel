<?php


namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\EventContact;

class EventContactCreated
{
    use SerializesModels;


    public function __construct(
        public EventContact $eventContact
    )
    {
    }
}
