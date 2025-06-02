<?php

namespace App\Helpers\Front;

use App\Enum\ParticipantType;
use App\Models\Event;

class FrontTextHelper
{

    public static function getConnexionPageText(Event $event, string $registrationType): string
    {
        return match ($registrationType) {
            ParticipantType::CONGRESS->value => $event->texts->fo_login_participant,
            'industry' => $event->texts->fo_login_industry,
            'speaker' => $event->texts->fo_login_speaker,
            'group' => $event->texts->fo_group,
            'sponsor' => $event->texts->fo_exhibitor,
            default => $event->texts->fo_login_participant,
        };
    }
}
