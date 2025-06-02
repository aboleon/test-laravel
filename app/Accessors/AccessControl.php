<?php

namespace App\Accessors;

use App\Models\Event;
use App\Models\EventManager\Accommodation;
use App\Models\Order;

class AccessControl
{

    public static function eventAccommodation(Event $event, Accommodation $accommodation)
    {
        if ($accommodation->event_id != $event->id) {

            return redirect()->route('panel.event-error', ['event' => $event->id])->with([
                'event_error_message' => "Cet hébergement n'est pas associé à cet évènement.",
            ]);
        }
    }

    public static function eventOrder(Event $event, Order $order)
    {
        if ($order->event_id != $event->id) {

            return redirect()->route('panel.event-error', ['event' => $event->id])->with([
                'event_error_message' => "Cette commande n'est pas associée à cet évènement.",
            ]);
        }
    }
}
