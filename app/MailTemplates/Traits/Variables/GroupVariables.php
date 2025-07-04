<?php

namespace App\MailTemplates\Traits\Variables;

trait GroupVariables
{

    public function GROUPES_Nom_Groupe(): string
    {
        return $this->eventContact?->eventGroup?->group?->name ?? '';
    }

    public function GROUPES_Prenom_Nom_Responsable(): string {
        return $this->eventContact?->eventGroup?->mainContact?->names() ?? '';
    }

}
