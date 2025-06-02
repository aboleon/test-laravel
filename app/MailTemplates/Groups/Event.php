<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Event implements GroupVariables
{
    public static function signature(): string
    {
        return 'wg_event';
    }

    public static function variables(): array
    {
        return [
            'Acronyme' => 'EVENT_Acronyme',
            'Adresse' => 'EVENT_Adresse',
            'Admin grant mobile' => 'EVENT_MobileGrant',
            'Admin grant nom' => 'EVENT_NomGrant',
            'Admin grant prénom' => 'EVENT_PrenomGrant',
            'Admin inscription Email' => 'EVENT_EmailRespInscription',
            'Admin inscription mobile' => 'EVENT_TelRespInscription',
            'Admin inscription Nom' => 'EVENT_NomRespInscription',
            'Admin inscription Prénom' => 'EVENT_PrenomRespInscription',
            'Date début' => 'EVENT_Date_Debut',
            'Date fin' => 'EVENT_Date_Fin',
            'Lieu' => 'EVENT_Lieu',
            'Nom de l\'évent' => 'EVENT_Nom',
            'Pays' => 'EVENT_Pays',
            'Photo de l\'évenement' => 'EVENT_Photo',
            'Type d\'évènement' => 'EVENT_Type',
            'Url de connexion front' => 'EVENT_Url',
            'Ville' => 'EVENT_Ville',
        ];
    }

    public static function title(): string
    {
        return 'Évènement';
    }

    public static function icon(): string
    {
        return 'bookmark';
    }

}
