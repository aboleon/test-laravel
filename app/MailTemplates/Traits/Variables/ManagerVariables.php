<?php

namespace App\MailTemplates\Traits\Variables;

trait ManagerVariables
{
    public function MANAGER_Prenom(): string
    {
        return $this->event->admin?->first_name ?? '';
    }

    public function MANAGER_Nom(): string
    {
        return $this->event->admin?->last_name ?? '';
    }

    public function MANAGER_Email(): string
    {
        return $this->event->admin?->email ?? '';
    }

    public function MANAGER_Fonction(): string
    {
        return $this->event->admin?->profile?->job ?? '';
    }

    public function MANAGER_LigneDirecte(): string
    {
        return $this->event->admin?->profile?->phone ?? '';
    }

    public function MANAGER_mobile(): string
    {
        return $this->event->admin?->profile?->mobile ?? '';
    }
}
