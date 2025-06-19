<?php

declare(strict_types=1);

namespace App\Exports\EventContact;

use App\Accessors\Dictionnaries;
use App\Enum\ClientType;
use App\Exports\EventContact\Abstract\AccountExportAbstract;
use App\Models\EventContact;
use MetaFramework\Accessors\Countries;

class AllGlobalExport extends AccountExportAbstract
{

    public function __construct() {
        parent::__construct();
    }

    public static function getFieldsMapping(): array
    {
        return [
            'pec' => [
                'type' => 'mandatory',
                'name' => 'PEC',
            ],
            'participation_type' => [
                'type' => 'mandatory',
                'name' => 'Type de participation',
            ],
            'domaine' => [
                'type' => 'mandatory',
                'name' => 'Domaine',
            ],
            'rpps' => [
                'type' => 'optional',
                'name' => 'RPPS',
            ],
            'titre' => [
                'type' => 'optional',
                'name' => 'Titre',
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
                'type' => 'mandatory',
                'name' => 'E-mail',
            ],
            'phone' => [
                'type' => 'mandatory',
                'name' => 'Téléphone portable',
            ],
            'fonction' => [
                'type' => 'mandatory',
                'name' => 'Fonction',
            ],
            'raison_sociale' => [
                'type' => 'optional',
                'name' => 'Raison sociale',
            ],
            'caution' => [
                'type' => 'mandatory',
                'name' => 'Caution',
            ],
            'caution_prestation' => [
                'type' => 'mandatory',
                'name' => 'Caution prestation',
            ],
            'services' => [
                'type' => 'mandatory',
                'name' => 'Prestation',
            ],
            'hotel' => [
                'type' => 'mandatory',
                'name' => 'Hotel',
            ],
            'check-in' => [
                'type' => 'mandatory',
                'name' => 'Check-in',
            ],
            'check-out' => [
                'type' => 'mandatory',
                'name' => 'Check-out',
            ],
            'accompagnant' => [
                'type' => 'mandatory',
                'name' => 'Accompagnant',
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
                'type' => 'optional',
                'name' => 'Pays EN',
            ],
            'mail_2' => [
                'type' => 'optional',
                'name' => 'Mail 2',
            ],
            'phone_2' => [
                'type' => 'optional',
                'name' => 'Téléphone 2',
            ],
            'presence' => [
                'type' => 'optional',
                'name' => 'Présence',
            ],
            'transport_etat_aller' => [
                'type' => 'optional',
                'name' => 'Transport état aller',
            ],
            'transport_etat_retour' => [
                'type' => 'optional',
                'name' => 'Transport état retour',
            ],
            'date_rattachement' => [
                'type' => 'optional',
                'name' => 'Date Rattachement',
            ],
        ];
    }


    protected function buildDataRow(EventContact $row): array {
        return [
            'pec' => $this->eventContactAccessor->hasPaidGrantDeposit() ? 'Oui' : 'Non',
            'participation_type' => $row->participationType?->name,
            'domaine' => Dictionnaries::entry('domain', $row->profile->domain_id)?->name,
            'rpps' => $row->profile->rpps,
            'titre' => Dictionnaries::entry('titles', $row->profile->title_id)?->name,
            'prenom' => $row->account->first_name,
            'nom' => $row->account->last_name,
            'email' => $this->accountAccessor->getEmail(),
            'phone' => $this->accountAccessor->defaultPhone('phone', true),
            'fonction' => $row->profile->function,
            'raison_sociale' => $row->profile->company_name,
            'caution' => $this->eventContactAccessor->getDepositedGrant()?->title,
            'caution_prestation' => implode("\n", $this->serviceData['caution_prestation_titles']),
            'hotel' => implode("\n", $this->accommodationData['hotel_names']),
            'check-in' => implode("\n", $this->accommodationData['check_ins']),
            'check-out' => implode("\n", $this->accommodationData['check_outs']),
            'accompagnant' => implode("\n", $this->accommodationData['accompagnants']),
            'solde_ttc' => $this->eventContactAccessor->getAllRemainingPayments(),
            'comment' => $row->profile->notes,
            'locality' => $this->address?->locality,
            'departement' => $this->address?->administrative_area_level_2,
            'region' => $this->address?->administrative_area_level_1,
            'pays' => Countries::getCountryNameByCodeAndLocale($this->address?->country_code),
            'pays_en' => Countries::getCountryNameByCodeAndLocale($this->address?->country_code, 'en'),
            'mail_2' => $row->account->mails?->first()?->email,
            'phone_2' => $this->accountAccessor->secondaryPhone('phone', true),
            'presence' => $row->is_attending ? 'Oui' : 'Non',
            'transport_etat_aller' => Dictionnaries::entry('transport_step', $row->transport?->departure_step)?->name,
            'transport_etat_retour' => Dictionnaries::entry('transport_step', $row->transport?->return_step)?->name,
            'date_rattachement' => $row->created_at?->format('d/m/Y')
        ];
    }


}
