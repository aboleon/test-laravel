<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Group implements GroupVariables
{
    public static string $prefix = 'GROUPES_';

    public static function variables(): array
    {
        return [
            self::$prefix.'Nom_Groupe' => 'Intitulé groupe',
            self::$prefix.'Nom_Responsable' => 'Nom contact admin groupe',
            self::$prefix.'Prenom_Responsable' => 'Prénom contact admin groupe',
        ];
    }

    public static function title(): string
    {
        return 'Groupe';
    }

    public static function icon(): string
    {
        return 'table-cell-properties';
    }
}
