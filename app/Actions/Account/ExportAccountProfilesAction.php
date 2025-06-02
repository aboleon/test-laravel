<?php

namespace App\Actions\Account;

use App\Actions\AccountProfile\View\AccountProfileExportView;
use App\Actions\Export\BaseExportAction;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportAccountProfilesAction extends BaseExportAction
{
    public static $fieldsMapping = [
        'id' => 'ID du profil',
        'first_name' => 'Prénom',
        'last_name' => 'Nom de famille',
        'account_type' => 'Type de compte',
        'base' => 'Base',
        'domain' => 'Domaine',
        'title' => 'Titre',
        'profession' => 'Profession',
        'savant_society' => 'Société savante',
        'civ' => 'Civilité',
        'birth' => 'Date de naissance',
        'cotisation_year' => 'Année de cotisation',
        'blacklisted' => 'Blackliste',
        'created_by' => 'Créé par',
        'blacklist_comment' => 'Commentaire blackliste',
        'notes' => 'Notes',
        'function' => 'Fonction',
        'passport_first_name' => 'Prénom passeport',
        'passport_last_name' => 'Nom passeport',
        'rpps' => 'Rpps',
        //
        'establishment_name' => 'Nom de l\'établissement',
        'establishment_country_code' => 'Code pays de l\'établissement',
        'establishment_type' => 'Type d\'établissement',
        'establishment_street_number' => 'Numéro de rue de l\'établissement',
        'establishment_postal_code' => 'Code postal de l\'établissement',
        'establishment_locality' => 'Localité de l\'établissement',
        'establishment_administrative_area_level_1' => 'Niveau administratif 1 de l\'établissement',
        'establishment_administrative_area_level_2' => 'Niveau administratif 2 de l\'établissement',
        'establishment_text_address' => 'Adresse textuelle de l\'établissement',
        //
        'main_address_billing' => 'Facturation de l\'adresse principale',
        'main_address_street_number' => 'Numéro de rue de l\'adresse principale',
        'main_address_route' => 'Itinéraire de l\'adresse principale',
        'main_address_locality' => 'Localité de l\'adresse principale',
        'main_address_postal_code' => 'Code postal de l\'adresse principale',
        'main_address_country_code' => 'Code pays de l\'adresse principale',
        'main_address_administrative_area_level_1' => 'Niveau administratif 1 de l\'adresse principale',
        'main_address_administrative_area_level_2' => 'Niveau administratif 2 de l\'adresse principale',
        'main_address_text_address' => 'Adresse textuelle principale',
        'main_address_company' => 'Société de l\'adresse principale',
        //
        'main_phone' => 'Téléphone principal',
    ];


    public function __construct(
        protected array $userIds,
        protected array $fields
    )
    {
    }

    protected function prepareSheet($sheet): void
    {
        //--------------------------------------------
        // header
        //--------------------------------------------
        $columnIndex = 'A';
        foreach ($this->fields as $field) {
            $fieldLabel = self::$fieldsMapping[$field];
            $sheet->setCellValue($columnIndex . '1', $fieldLabel);

            $sheet->getColumnDimension($columnIndex)->setAutoSize(true);
            $sheet->getStyle($columnIndex . '1')->getFont()->setBold(true);
            $columnIndex++;
        }

        // background color
        $lastHeaderColumn = --$columnIndex;
        $sheet->getStyle('A1:' . $lastHeaderColumn . '1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('D3D3D3');


        //--------------------------------------------
        // body
        //--------------------------------------------
        $profiles = AccountProfileExportView::whereIn('user_id', $this->userIds)->get()->toArray();

        $rowIndex = 2;
        foreach ($profiles as $profile) {
            $columnIndex = 'A';
            foreach ($this->fields as $field) {
                $value = $profile[$field] ?? null;

                if ($field == 'main_phone' && $value) {
                    $value = " " . $value; // make the plus prefix of the phone interpreted as text (ie not a formula)
                }

                $sheet->setCellValue($columnIndex . $rowIndex, $value);
                $sheet->getStyle($columnIndex . $rowIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $columnIndex++;
            }
            $rowIndex++;
        }

    }
}