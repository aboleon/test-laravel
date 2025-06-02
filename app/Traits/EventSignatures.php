<?php

namespace App\Traits;

use App\Traits\Models\EventModelTrait;

trait EventSignatures
{
    use EventModelTrait;
    public function adminSignature(): string
    {
        return $this->event->adminSubs->names().'<br>'.$this->event->adminSubs->email.' - '.$this->event->adminSubs->profile->phone.' / '.$this->event->adminSubs->profile->mobile;
    }

    public function adminGrantSignature(): string
    {
        return $this->event->pec?->grantAdmin?->names().'<br>'.$this->event->pec?->grantAdmin?->email.' - '.$this->event->pec?->grantAdmin?->profile?->phone.' / '.$this->event->pec?->grantAdmin?->profile?->mobile;
    }
}
