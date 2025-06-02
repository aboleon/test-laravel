<?php

namespace App\Services\Filters\Data;

use App\Accessors\Users;
use App\Accessors\Vats;
use App\Services\Filters\FilterParser;
use App\Services\Filters\Interfaces\FilterProviderInterface;
use MetaFramework\Accessors\Countries;

/**
 * Shared filters that are common across providers
 */
class GroupFilters implements FilterProviderInterface
{
    public static function getFilters(FilterParser $distributor): array
    {
        return [
            // Group basic information
            [
                'id'        => 'groups.name',
                'label'     => 'Intitulé / Nom du groupe',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'groups.company',
                'label'     => 'Raison sociale',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'groups.billing_comment',
                'label'     => 'Commentaire facturation',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'groups.siret',
                'label'     => 'Siret',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'groups.vat_id',
                'label'     => 'Taux de TVA',
                'type'      => 'integer',
                'input'     => 'select',
                'values'    => Vats::selectable(),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'groups.phone',
                'label'     => 'Numéro de téléphone',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],

            // Creator information (nested under users table)

            [
                'id'        => 'groups.created_by',
                'label'     => 'Créé par liste',
                'type'      => 'integer',
                'input'     => 'select',
                'values'    => Users::adminUsersSelectable(),
                'related' => 'users.id = groups.created_by',
                'operators' => 'non_nullable_select_operators',
            ],
            [
                'nested'  => 'creator_users',
                'label'   => 'Créé par',
                'related' => 'users.id = groups.created_by',
                'entries' => [
                    [
                        'id'        => 'first_name',
                        'label'     => 'Prénom',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'last_name',
                        'label'     => 'Nom de famille',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                    [
                        'id'        => 'email',
                        'label'     => 'Email',
                        'type'      => 'string',
                        'operators' => 'string_operators',
                    ],
                ],
            ],

            // Address
            [
                'nested'  => 'group_address',
                'label'   => 'Adresse principale',
                'related' => 'group_address.group_id = groups.id',
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
                ],
            ],
        ];
    }
}
