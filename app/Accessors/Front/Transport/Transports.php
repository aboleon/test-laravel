<?php

namespace App\Accessors\Front\Transport;

use App\Enum\DesiredTransportManagement;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Transport\EventTransport;

class Transports
{
    public static function getDashboardItems(Event $event, EventContact $eventContact)
    {

        $et = EventTransport::where('events_contacts_id', $eventContact->id)->first();


        if(!$et || !$et->request_completed){
            return [];
        }


        $text = match ($et->desired_management) {
            DesiredTransportManagement::DIVINE->value => "Je souhaite que l'organisateur gère mon transport.",
            DesiredTransportManagement::PARTICIPANT->value => "Je gère mon transport tout seul.",
            DesiredTransportManagement::UNNECESSARY->value => "Je n'ai pas besoin de transport.",
            default => "Je n'ai pas encore statué sur le mode de transport.",
        };

        return [
            [
                'text' => $text,
            ],
        ];

    }

}