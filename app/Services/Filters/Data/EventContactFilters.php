<?php

namespace App\Services\Filters\Data;

use App\Accessors\EventAccessor;
use App\Accessors\GroupAccessor;
use App\Enum\EstablishmentType;
use App\Enum\RegistrationType;
use App\Services\Filters\FilterParser;
use App\Services\Filters\Interfaces\FilterProviderInterface;

/**
 * Shared filters that are common across providers
 */
class EventContactFilters implements FilterProviderInterface
{
    public static function getFilters(FilterParser $distributor): array
    {
        $event_id = $distributor->getEventId();

        return [
            [
                'id'        => 'events_contacts.created_at',
                'label'     => 'Date de rattachement',
                'type'      => 'date',
                'input'     => 'function (rule, inputName) {
        let s = \'<input name="created_at" type="text" data-type="flatpickr" class="query-builder-flatpickr" data-conf="allowInput=true;dateFormat=Y-m-d;altInput=true;altFormat='.config('app.date_display_format').'" />\';
        return swapPlaceholderWithName(s, \'created_at\', inputName);
    }',
                'operators' => 'date_operators',
            ],
            [
                'id'        => 'events_contacts.participation_type_id',
                'label'     => 'Type de participation liste',
                'type'      => 'string',
                'input'     => 'select',
                'values'    => (new EventAccessor())->setEvent($event_id)->representedParticipationTypes(),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'participation_types.name',
                'label'     => 'Type de participation',
                'type'      => 'string',
                'operators' => 'string_operators',
                'related'   => 'events_contacts.participation_type_id=participation_types.id',
                'parse'     => 'json',
            ],
            [
                'id'        => 'events_contacts.pec_eligible',
                'label'     => 'Eligible PEC',
                'type'      => 'integer',
                'input'     => 'radio',
                'values'    => [
                    1 => 'Oui',
                    0 => 'Non',
                ],
                'operators' => 'boolean_operators',
            ],
            [
                'id'        => 'events_contacts.pec_enabled',
                'label'     => 'PEC',
                'type'      => 'integer',
                'input'     => 'radio',
                'values'    => [
                    1 => 'Oui',
                    0 => 'Non',
                ],
                'operators' => 'boolean_operators',
            ],
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
                        'values'    => (new EventAccessor())->setEvent($event_id)->representedCountries(),
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
                'values'    => (new EventAccessor())->setEvent($event_id)->representedEstablishments(),
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
                        'values'    => (new EventAccessor())->setEvent($event_id)->representedEstablishmentCountries(),
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
            //----------------------------------------
            // Event contacts
            //----------------------------------------
            [
                'id'        => 'events_contacts.registration_type',
                'label'     => 'Type de souscription',
                'type'      => 'string',
                'input'     => 'select',
                'values'    => RegistrationType::translations(),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'events_contacts.is_attending',
                'label'     => 'Présence congrès',
                'type'      => 'integer',
                'input'     => 'radio',
                'values'    => [
                    1 => 'Oui',
                    0 => 'Non',
                ],
                'operators' => 'boolean_operators',
            ],
            [
                'id'        => 'events_contacts.comment',
                'label'     => 'Commentaire',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'event_groups.group',
                'label'     => 'Groupe',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'event_groups.group_ids',
                'label'     => 'Groupe liste',
                'type'      => 'string',
                'input'     => 'select',
                'values'    => collect(GroupAccessor::getGroupsSelectableByEventId($event_id))->mapWithKeys(function ($item, $key) {
                    return [",$key," => $item];
                })->toArray(),
                'operators' => 'select_operators',
            ],
        ];
    }
}
