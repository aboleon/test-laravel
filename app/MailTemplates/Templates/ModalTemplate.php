<?php

namespace App\MailTemplates\Templates;

use App\MailTemplates\Contracts\Template;
use App\MailTemplates\Traits\MailTemplate;
use App\Models\Event;
use App\Traits\EventCommons;

class ModalTemplate implements Template
{
    use MailTemplate;
    use EventCommons;
    public $banner = null;

    public function __construct(public Event  $event)
    {

        $this->banner = $this->getBanner($this->event, 'banner_large');
    }

    public function signature(): string
    {
        return 'modaltemplate';
    }

    public function variables(): array
    {
        return [];
    }

    public function computed(): array
    {
        return [];
    }


}
