<?php

namespace App\Services\Filters\Data;

use App\Accessors\Dictionnaries;
use App\Accessors\Users;
use App\Enum\ClientType;
use App\Helpers\HtmlControlHelper;
use App\Services\Filters\FilterParser;
use App\Services\Filters\Interfaces\FilterProviderInterface;

/**
 * Shared filters that are common across providers
 */
class SharedAccountFilters implements FilterProviderInterface
{
    public static function getFilters(FilterParser $distributor): array
    {
        $event_id = $distributor->getEventId();

        return [
            [
                'id'        => 'users.first_name',
                'label'     => 'Prénom',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'users.last_name',
                'label'     => 'Nom',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'users.email',
                'label'     => 'Email',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_profile.account_type',
                'label'     => 'Type de compte',
                'type'      => 'string',
                'input'     => 'select',
                'values'    => ClientType::translations(),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'account_profile.lang',
                'label'     => 'Langue',
                'type'      => 'string',
                'input'     => 'select',
                'values'    => ['fr' => 'Français', 'en' => 'English'],
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'account_profile.function',
                'label'     => 'Fonction',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_profile.base_id',
                'label'     => 'Base liste',
                'type'      => 'integer',
                'input'     => 'select',
                'values'    => Dictionnaries::selectValues("base"),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'dictionnary_entries.name',
                'label'     => 'Base',
                'type'      => 'string',
                'operators' => 'string_operators',
                'related'   => 'account_profile.base_id=dictionnary_entries.id',
                'parse'     => 'json',
            ],
            [
                'id'        => 'account_profile.domain_id',
                'label'     => 'Domaine liste',
                'type'      => 'integer',
                'input'     => 'select',
                'values'    => Dictionnaries::selectValues("domain"),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'dictionnary_entries.name',
                'label'     => 'Domaine',
                'type'      => 'string',
                'operators' => 'string_operators',
                'related'   => 'account_profile.domain_id=dictionnary_entries.id',
                'parse'     => 'json',
            ],

            [
                'id'        => 'account_profile.profession_id',
                'label'     => 'Profession liste',
                'type'      => 'integer',
                'input'     => 'function (rule, inputName) {
            let s = ' . json_encode(HtmlControlHelper::generateGroupedSelect("profession_id", Dictionnaries::selectValues("professions"))) . ';
            return swapPlaceholderWithName(s, \'profession_id\', inputName);
        }',
                'operators' => 'select_operators',
            ],

            [
                'id'        => 'dictionnaries.profession',
                'label'     => 'Profession',
                'type'      => 'string',
                'operators' => 'string_operators',
                // TODO: related
            ],
            [
                'id'        => 'account_profile.savant_society_id',
                'label'     => 'Société savante liste',
                'type'      => 'integer',
                'input'     => 'select',
                'values'    => Dictionnaries::selectValues("savant_societies"),
                'operators' => 'select_operators',
            ],
            [
                'id'        => 'dictionnary_entries.name',
                'label'     => 'Société savante',
                'type'      => 'string',
                'operators' => 'string_operators',
                'related'   => 'account_profile.savant_society_id=dictionnary_entries.id',
                'parse'     => 'json',
            ],
            [
                'id'        => 'account_profile.birth',
                'label'     => 'Date de naissance',
                'type'      => 'date',
                'input'     => 'function (rule, inputName) {
            let s = \'<input name="birth" type="text" data-type="flatpickr" class="query-builder-flatpickr" data-conf="allowInput=true;dateFormat=Y-m-d;altInput=true;altFormat='.config('app.date_display_format').'" />\';
            return swapPlaceholderWithName(s, \'birth\', inputName);
        }',
                'operators' => 'date_operators',
            ],
            [
                'id'    => 'account_profile.cotisation_year',
                'label' => 'Année de cotisation',
                'type'  => 'integer',
                'input' => 'number',
            ],
            [
                'id'        => 'account_profile.blacklisted',
                'label'     => 'Blackliste',
                'type'      => 'date',
                'input'     => 'function (rule, inputName) {
            let s = \'<input name="blacklisted" type="text" data-type="flatpickr" class="query-builder-flatpickr" data-conf="enableTime=true;enableSeconds=true;minuteIncrement=01;time_24hr=true;allowInput=true;dateFormat=Y-m-d H:i:S;altInput=true;altFormat='.config('app.date_display_format').' H:i:S" />\';
            return swapPlaceholderWithName(s, \'blacklisted\', inputName);
        }',
                'operators' => 'date_operators',
            ],
            [
                'id'        => 'account_profile.created_by',
                'label'     => 'Créé par',
                'type'      => 'integer',
                'input'     => 'function (rule, inputName) {
            let s = ' . json_encode(self::generateCreatedBy('account_profile.created_by')) . ';
            return swapPlaceholderWithName(s, \'profession_id\', inputName);
        }',
                'operators' => 'non_nullable_select_operators',
            ],
            [
                'id'        => 'account_profile.blacklist_comment',
                'label'     => 'Commentaire blackliste',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_profile.notes',
                'label'     => 'Notes',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_profile.passport_first_name',
                'label'     => 'Prénom passeport',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_profile.passport_last_name',
                'label'     => 'Nom passeport',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_profile.rpps',
                'label'     => 'Rpps',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
            [
                'id'        => 'account_phones.phone',
                'label'     => 'Téléphone',
                'type'      => 'string',
                'operators' => 'string_operators',
            ],
        ];
    }

    private static function generateCreatedBy(string $name): string
    {
        $html = '<select class="form-select" name="'.htmlspecialchars($name).'">';
        $html .= '<optgroup label="Administrateurs">';

        foreach (Users::adminUsersSelectable() as $id => $names) {
            $html .= '<option value="'.htmlspecialchars($id).'">'.htmlspecialchars($names).'</option>';
        }
        $html .= '</optgroup>';
        $html .= '<optgroup label="Autre">';
        $html .= '<option value="function_createdFront">Création front</option>';
        $html .= '</optgroup>';
        $html .= '</select>';

        return $html;
    }
}
