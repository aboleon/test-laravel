<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Manager implements GroupVariables
{

    public static string $prefix = 'MANAGER_';

    public static function variables(): array
    {
        return [
            self::$prefix.'Email' => 'Email',
            self::$prefix.'Fonction' => 'Fonction',
            self::$prefix.'LigneDirecte' => 'Ligne directe',
            self::$prefix.'Nom' => 'Nom',
            self::$prefix.'mobile' => 'Numéro de téléphone mobile',
            self::$prefix.'Prenom' => 'Prénom',
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
