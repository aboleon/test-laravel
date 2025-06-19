<?php

namespace App\Services\Filters\Data;

use App\Accessors\Establishments;
use App\Enum\EstablishmentType;
use App\Services\Filters\FilterParser;
use App\Services\Filters\Interfaces\FilterProviderInterface;
use MetaFramework\Accessors\Countries;

/**
 * Shared filters that are common across providers
 */
class AccountFilters implements FilterProviderInterface
{
    public static function getFilters(FilterParser $distributor): array
    {
        return [
            [
                'nested'  => 'account_address',
                'label'   => 'Adresse',
                'entries' => [
                    [
                        'id'        => 'billing',
                        'label'     => 'Adresse de facturation',
                        'type'      => 'integer',
                        'input'     => 'radio',
                        'values'    => [
                            1 => 'Oui',
                            0 => 'Non',
                        ],
                        'operators' => 'boolean_operators',
                    ],
                    [
                        'id'        => 'country_code',
                        'label'     => 'Pays',
                        'type'      => 'string',
                        'input'     => 'select',
                        'values'    => Countries::orderedCodeNameArray(),
                        'operators' => 'select_operators',
                    ],
                    [
                        'id'        => 'locality',
                        'label'     => 'Localité',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'postal_code',
                        'label'     => 'Code postal',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'route',
                        'label'     => 'Rue',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'company',
                        'label'     => 'Entreprise',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                ],
            ],
            [
                'id'        => 'account_profile.establishment_id',
                'label'     => 'Etablissements liste',
                'type'      => 'string',
                'input'     => 'select',
                'values'    => (new Establishments())->orderedIdNameArray(),
                'operators' => 'select_operators',
            ],
            [
                'nested'  => 'establishments',
                'label'   => 'Etablissement',
                'related' => 'establishments.id = account_profile.establishment_id',
                'entries' => [
                    [
                        'id'        => 'type',
                        'label'     => 'Type',
                        'type'      => 'integer',
                        'input'     => 'radio',
                        'values'    => EstablishmentType::translations(),
                        'operators' => 'boolean_operators',
                    ],
                    [
                        'id'        => 'name',
                        'label'     => 'Nom',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'country_code',
                        'label'     => 'Pays',
                        'type'      => 'string',
                        'input'     => 'select',
                        'values'    => (new Establishments())->representedEstablishmentCountries(),
                        'operators' => 'select_operators',
                    ],
                    [
                        'id'        => 'locality',
                        'label'     => 'Localité',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'postal_code',
                        'label'     => 'Code postal',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'route',
                        'label'     => 'Rue',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                ],
            ],
        ];
    }
}
