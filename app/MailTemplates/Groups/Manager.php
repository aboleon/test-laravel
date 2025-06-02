<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Manager implements GroupVariables
{

    public static function variables(): array
    {
        return [
            'Email' => 'AdminEvent_Email',
            'Fonction' => 'AdminEvent_Fonction',
            'Ligne directe' => 'AdminEvent_LigneDirecte',
            'Nom' => 'AdminEvent_Nom',
            'Numéro de téléphone mobile' => 'AdminEvent_mobile',
            'Prénom' => 'AdminEvent_Prenom',
        ];
    }

    public static function title(): string
    {
        return 'Admin Évènement';
    }

    public static function icon(): string
    {
        return 'user';
    }

}
