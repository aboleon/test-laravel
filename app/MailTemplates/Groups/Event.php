<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Event implements GroupVariables
{
    public static string $prefix = 'EVENT_';

    public static function signature(): string
    {
        return 'wg_event';
    }

    public static function variables(): array
    {
        return [
            self::$prefix.'Acronyme' => 'Acronyme',
            self::$prefix.'Adresse' => 'Adresse',
            self::$prefix.'MobileGrant' => 'Admin grant mobile',
            self::$prefix.'NomGrant' => 'Admin grant nom',
            self::$prefix.'PrenomGrant' => 'Admin grant prénom',
            self::$prefix.'EmailRespInscription' => 'Admin inscription Email',
            self::$prefix.'TelRespInscription' => 'Admin inscription mobile',
            self::$prefix.'NomRespInscription' => 'Admin inscription Nom',
            self::$prefix.'PrenomRespInscription' => 'Admin inscription Prénom',
            self::$prefix.'Clients' => 'Clients',
            self::$prefix.'Date_Debut' => 'Date début',
            self::$prefix.'Date_Fin' => 'Date fin',
            self::$prefix.'Lieu' => 'Lieu',
            self::$prefix.'Nom' => 'Nom de l\'évent',
            self::$prefix.'Pays' => 'Pays',
            self::$prefix.'Photo' => 'Photo de l\'évenement',
            self::$prefix.'Type' => 'Type d\'évènement',
            self::$prefix.'Url' => 'Url de connexion front',
            self::$prefix.'Ville' => 'Ville',
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
