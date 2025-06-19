<?php

declare(strict_types=1);

namespace App\Exports\EventContact;

use App\Accessors\Dictionnaries;
use App\Enum\ClientType;
use App\Exports\EventContact\Abstract\AccountExportAbstract;
use App\Models\EventContact;
use MetaFramework\Accessors\Countries;

class CongressExport extends AccountExportAbstract
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getFieldsMapping(): array
    {
        return [
            'pec'                => [
                'type' => 'mandatory',
                'name' => 'PEC',
            ],
            'participation_type' => [
                'type' => 'mandatory',
                'name' => 'Type de participation',
            ],
            'domaine'            => [
                'type' => 'optional',
                'name' => 'Domaine',
            ],
            'titre'              => [
                'type' => 'optional',
                'name' => 'Titre',
            ],
            'prenom'             => [
                'type' => 'mandatory',
                'name' => 'Prénom',
            ],
            'nom'                => [
                'type' => 'mandatory',
                'name' => 'Nom',
            ],
            'email'              => [
                'type' => 'optional',
                'name' => 'E-mail',
            ],
            'fonction'           => [
                'type' => 'optional',
                'name' => 'Fonction',
            ],
            'raison_sociale'     => [
                'type' => 'optional',
                'name' => 'Raison sociale',
            ],
            'etablissement'      => [
                'type' => 'optional',
                'name' => 'Etablissement',
            ],
            'services'           => [
                'type' => 'mandatory',
                'name' => 'Prestation',
            ],
            'solde_ttc'          => [
                'type' => 'mandatory',
                'name' => 'Solde TTC',
            ],
            'comment'            => [
                'type' => 'mandatory',
                'name' => 'Commentaire',
            ],
            'locality'           => [
                'type' => 'mandatory',
                'name' => 'Ville',
            ],
            'departement'        => [
                'type' => 'optional',
                'name' => 'Département',
            ],
            'region'             => [
                'type' => 'optional',
                'name' => 'Région',
            ],
            'pays'               => [
                'type' => 'mandatory',
                'name' => 'Pays',
            ],
            'pays_en'            => [
                'type' => 'optional',
                'name' => 'Pays EN',
            ],
        ];
    }

    protected function getRelations(): array
    {
        return [
            'event',
            'account',
            'account.address',
            'account.mails',
            'profile',
            'profile.establishment',
            'grantDeposit',
            'participationType',
        ];
    }


    protected function buildDataRow(EventContact $row): array
    {
        return [
            'pec'                => $this->eventContactAccessor->hasPaidGrantDeposit() ? 'Oui' : 'Non',
            'participation_type' => $row->participationType?->name,
            'domaine'            => Dictionnaries::entry('domain', $row->profile->domain_id)?->name,
            'titre'              => Dictionnaries::entry('titles', $row->profile->title_id)?->name,
            'prenom'             => $row->account->first_name,
            'nom'                => $row->account->last_name,
            'email'              => $this->accountAccessor->getEmail(),
            'fonction'           => $row->profile->function,
            'raison_sociale'     => $row->profile->company_name,
            'etablissement'      => $row->profile->establishment?->name,
            'solde_ttc'          => $this->eventContactAccessor->getAllRemainingPayments(),
            'comment'            => $row->profile->notes,
            'locality'           => $this->address?->locality,
            'departement'        => $this->address?->administrative_area_level_2,
            'region'             => $this->address?->administrative_area_level_1,
            'pays'               => Countries::getCountryNameByCodeAndLocale($this->address?->country_code),
            'pays_en'            => Countries::getCountryNameByCodeAndLocale($this->address?->country_code, 'en'),
        ];
    }


}
