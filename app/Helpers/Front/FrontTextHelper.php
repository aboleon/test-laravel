<?php

namespace App\Helpers\Front;

use App\Enum\ParticipantType;
use App\Models\Event;

class FrontTextHelper
{

    public static function getConnexionPageText(Event $event, string $registrationType): string
    {
        return match ($registrationType) {
           // ParticipantType::CONGRESS->value => nl2br($event->texts->fo_login_participant),
            'industry' => nl2br($event->texts->fo_login_industry),
            'speaker' => nl2br($event->texts->fo_login_speaker),
            'group' => nl2br($event->texts->fo_group),
            'sponsor' => nl2br($event->texts->fo_exhibitor),
            default => nl2br($event->texts->fo_login_participant),
        };
    }
}
