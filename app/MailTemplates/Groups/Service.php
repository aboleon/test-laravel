<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Service implements GroupVariables
{

    public static function variables(): array
    {
        return [
            'PRESTA_intitule',
            'PRESTA_adresse',
            'PRESTA_date',
            'PRESTA_heure',
            'PRESTA_commentaire'
        ];
    }

    public static function title(): string
    {
        return 'Personnalisation Prestation';
    }

    public static function icon(): string
    {
        return '<i class="fa-solid fa-mug-saucer"></i>';
    }

}
