<?php

namespace App\MailTemplates\Traits;


use App\MailTemplates\Traits\Variables\EventVariables;
use App\MailTemplates\Traits\Variables\GroupVariables;
use App\MailTemplates\Traits\Variables\ManagerVariables;
use App\MailTemplates\Traits\Variables\ParticipantVariables;

Trait AllVariables
{
    use ManagerVariables;
    use EventVariables;
    use ParticipantVariables;
    use GroupVariables;
}
