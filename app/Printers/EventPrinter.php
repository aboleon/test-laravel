<?php

namespace App\Printers;

use App\Models\Event;

class EventPrinter
{
    public function __construct(public Event $event)
    {
    }

    public function names(): string
    {
        if (is_null($this->event->texts)) {
            return 'Non specifié';
        }

        $sub = '';
        if($this->event->texts->subname){
            $sub = '<span class="text-secondary d-none d-sm-inline-block px-sm-2">|</span><span class="d-block d-sm-inline-block text-secondary">' . $this->event->texts->subname . '</span>';
        }

        return '<span class="d-block d-sm-inline-block">' . $this->event->texts->name . '</span>' . $sub;
    }



}
