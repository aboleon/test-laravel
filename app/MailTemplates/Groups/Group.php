<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Group implements GroupVariables
{

    public static function variables(): array
    {
        return [
            'Intitulé groupe' => 'GROUPES_Nom_Groupe',
            'Nom contact admin groupe' => 'GROUPES_Nom_Responsable',
            'Prénom contact admin groupe' => 'GROUPES_Prenom_Responsable',
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
