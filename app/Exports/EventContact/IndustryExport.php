<?php

declare(strict_types=1);

namespace App\Exports\EventContact;

use App\Accessors\Dictionnaries;
use App\Exports\EventContact\Abstract\AccountExportAbstract;
use App\Models\EventContact;
use MetaFramework\Accessors\Countries;

class IndustryExport extends AccountExportAbstract
{

    public function __construct() {
        parent::__construct();
    }

    protected function getRelations(): array
    {
        return [
            'event',
            'account',
            'account.address',
            'account.phones',
            'account.mails',
            'eventGroup',
            'profile',
            'participationType',
        ];
    }

    public static function getFieldsMapping(): array
    {
        return [
            'participation_type' => [
                'type' => 'mandatory',
                'name' => 'Type de participation',
            ],
            'prenom' => [
                'type' => 'mandatory',
                'name' => 'Prénom',
            ],
            'nom' => [
                'type' => 'mandatory',
                'name' => 'Nom',
            ],
            'email' => [
                'type' => 'optional',
                'name' => 'E-mail',
            ],
            'raison_sociale' => [
                'type' => 'mandatory',
                'name' => 'Raison sociale',
            ],
            'group' => [
                'type' => 'mandatory',
                'name' => 'Société',
            ],
            'services' => [
                'type' => 'mandatory',
                'name' => 'Prestation',
            ],
            'solde_ttc' => [
                'type' => 'mandatory',
                'name' => 'Solde TTC',
            ],
            'comment' => [
                'type' => 'mandatory',
                'name' => 'Commentaire',
            ],
            'locality' => [
                'type' => 'mandatory',
                'name' => 'Ville',
            ],
            'departement' => [
                'type' => 'optional',
                'name' => 'Département',
            ],
            'region' => [
                'type' => 'optional',
                'name' => 'Région',
            ],
            'pays' => [
                'type' => 'mandatory',
                'name' => 'Pays',
            ],
            'pays_en' => [
                'type' => 'mandatory',
                'name' => 'Pays EN',
            ],
        ];
    }


    protected function buildDataRow(EventContact $row): array {
        return [
            'participation_type' => $row->participationType?->name,
            'prenom' => $row->account->first_name,
            'nom' => $row->account->last_name,
            'email' => $this->accountAccessor->getEmail(),
            'raison_sociale' => $row->profile->company_name,
            'group' => $row->eventGroup?->name,
            'solde_ttc' => $this->eventContactAccessor->getAllRemainingPayments(),
            'comment' => $row->profile->notes,
            'locality' => $this->address?->locality,
            'departement' => $this->address?->administrative_area_level_2,
            'region' => $this->address?->administrative_area_level_1,
            'pays' => Countries::getCountryNameByCodeAndLocale($this->address?->country_code),
            'pays_en' => Countries::getCountryNameByCodeAndLocale($this->address?->country_code, 'en'),
            'presence' => $row->is_attending ? 'Oui' : 'Non',
        ];
    }


}
