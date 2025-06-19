<?php

namespace App\Exports\EventContact;

use App\Enum\ParticipantType;

class ExportConfig
{

    public static function getExportablesGroups(): array
    {
        return [
            'all'                            => [
                [
                    'label' => 'Export Global',
                    'model' => AllGlobalExport::class,
                ],
            ],
            ParticipantType::INDUSTRY->value => [
                [
                    'label' => 'Export Global',
                    'model' => IndustryGlobalExport::class,
                ],
                [
                    'label' => 'Export',
                    'model' => IndustryExport::class,
                ],
            ],
            ParticipantType::CONGRESS->value => [
                [
                    'label' => 'Export Global',
                    'model' => CongressGlobalExport::class,
                ],
                [
                    'label' => 'Export',
                    'model' => CongressExport::class,
                ],
            ],
            ParticipantType::ORATOR->value   => [
                [
                    'label' => 'Export Global',
                    'model' => OratorGlobalExport::class,
                ],
            ],
        ];
    }
}
