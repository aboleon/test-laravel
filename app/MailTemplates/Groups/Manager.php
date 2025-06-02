<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Manager implements GroupVariables
{

    public static string $prefix = 'MANAGER_';

    public static function variables(): array
    {
        return [
            'Email'                      => self::$prefix.'_Email',
            'Fonction'                   => self::$prefix.'_Fonction',
            'Ligne directe'              => self::$prefix.'_LigneDirecte',
            'Nom'                        => self::$prefix.'_Nom',
            'Numéro de téléphone mobile' => self::$prefix.'_mobile',
            'Prénom'                     => self::$prefix.'_Prenom',
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
