<?php

namespace App\MailTemplates\Traits\Variables;

trait ManagerVariables
{
    public function AdminEvent_Prenom(): string
    {
        return $this->event->admin?->first_name ?? '';
    }

    public function AdminEvent_Nom(): string
    {
        return $this->event->admin?->last_name ?? '';
    }

    public function AdminEvent_Email(): string
    {
        return $this->event->admin?->email ?? '';
    }

    public function AdminEvent_Fonction(): string
    {
        return $this->event->admin?->profile?->job ?? '';
    }

    public function AdminEvent_LigneDirecte(): string
    {
        return $this->event->admin?->profile?->phone ?? '';
    }

    public function AdminEvent_mobile(): string
    {
        return $this->event->admin?->profile?->mobile ?? '';
    }
}
