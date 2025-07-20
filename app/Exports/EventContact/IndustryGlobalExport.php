<?php

declare(strict_types=1);

namespace App\Exports\EventContact;

use App\Accessors\Dictionnaries;
use App\Exports\EventContact\Abstract\AccountExportAbstract;
use App\Models\EventContact;
use MetaFramework\Accessors\Countries;

class IndustryGlobalExport extends AccountExportAbstract
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getFieldsMapping(): array
    {
        return [
            'participation_type' => [
                'type' => 'mandatory',
                'name' => 'Type de participation',
            ],
            'domaine'            => [
                'type' => 'mandatory',
                'name' => 'Domaine',
            ],
            'fonction'           => [
                'type' => 'mandatory',
                'name' => 'Fonction',
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
                'type' => 'mandatory',
                'name' => 'E-mail',
            ],
            'phone'              => [
                'type' => 'mandatory',
                'name' => 'Téléphone portable',
            ],
            'raison_sociale'     => [
                'type' => 'mandatory',
                'name' => 'Raison sociale',
            ],
            'group'              => [
                'type' => 'mandatory',
                'name' => 'Société',
            ],
            'prestation'         => [
                'type' => 'mandatory',
                'name' => 'Prestation',
            ],
            'solde_ttc'          => [
                'type' => 'mandatory',
                'name' => 'Solde TTC',
            ],
            'hotel'              => [
                'type' => 'mandatory',
                'name' => 'Hotel',
            ],
            'check-in'           => [
                'type' => 'mandatory',
                'name' => 'Check-in',
            ],
            'check-out'          => [
                'type' => 'mandatory',
                'name' => 'Check-out',
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
            'presence'           => [
                'type' => 'optional',
                'name' => 'Présence',
            ],
        ];
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


    protected function buildDataRow(EventContact $row): array
    {
        return [
            'participation_type' => $row->participationType?->name,
            'domaine'            => Dictionnaries::entry('domain', $row->profile->domain_id)?->name,
            'fonction'           => $row->profile->function,
            'prenom'             => $row->account->first_name,
            'nom'                => $row->account->last_name,
            'email'              => implode("\n", array_merge([$this->accountAccessor->getEmail()], $row->account->mails->pluck('email')->toArray())),
            'phone'              => $row->account->phones->map(function ($phone) {
                return $phone->getRawOriginal('phone');
            })->implode("\n"),
            'raison_sociale'     => $row->profile->company_name,
            'group'              => $row->eventGroup?->name,
            'solde_ttc'          => $this->eventContactAccessor->getAllRemainingPayments(),
            'hotel'              => implode("\n", $this->accommodationData['hotel_names']),
            'check-in'           => implode("\n", $this->accommodationData['check_ins']),
            'check-out'          => implode("\n", $this->accommodationData['check_outs']),
            'comment'            => $row->profile->notes,
            'locality'           => $this->address?->locality,
            'departement'        => $this->address?->administrative_area_level_2,
            'region'             => $this->address?->administrative_area_level_1,
            'pays'               => Countries::getCountryNameByCodeAndLocale($this->address?->country_code),
            'pays_en'            => Countries::getCountryNameByCodeAndLocale($this->address?->country_code, 'en'),
            'presence'           => $row->is_attending ? 'Oui' : 'Non',
        ];
    }


}
