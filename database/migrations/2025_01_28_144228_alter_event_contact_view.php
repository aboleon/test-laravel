<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_contact_view AS
        SELECT
    `ec`.`id` AS `id`,
    `e`.`id` AS `event_id`,
    `u`.`id` AS `user_id`,
    `u`.`first_name` AS `first_name`,
    `u`.`last_name` AS `last_name`,
    `u`.`email` AS `email`,
    JSON_UNQUOTE(JSON_EXTRACT(`d`.`name`, '$.fr')) AS `domain`,
    CASE
        WHEN `ap`.`account_type` = 'company' THEN 'Sociétés'
        WHEN `ap`.`account_type` = 'medical' THEN 'Professionnels de santé'
        WHEN `ap`.`account_type` = 'other' THEN 'Autres'
    END AS `account_type_display`,
    `ap`.`company_name` AS `company_name`,
    `a`.`locality` AS `locality`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `country`,
    JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')) AS `fonction`,
    GROUP_CONCAT(DISTINCT `g`.`name` SEPARATOR ', ') AS `group`,
    CONCAT(',', GROUP_CONCAT(DISTINCT `g`.`id` SEPARATOR ','), ',') AS `group_ids`,
    `ec`.`created_at` AS `created_at`,
    `ec`.`registration_type` AS `registration_type`,
    `ec`.`order_cancellation` AS `order_cancellation`,
    `ec`.`is_pec_eligible` AS `pec_eligible`,
    `ec`.`pec_enabled` AS `pec_enabled`,
    CASE WHEN `ec`.`pec_enabled` = 1 THEN 'Activée' ELSE NULL END AS `pec_enabled_display`,
    CASE WHEN `ec`.`is_pec_eligible` = 1 THEN 'Eligible' ELSE NULL END AS `pec_eligible_display`,
    `pt`.`group` AS `participation_type_group`,
    CASE
        WHEN `pt`.`group` IS NULL THEN '-'
        WHEN `pt`.`group` = 'congress' THEN 'Congressistes'
        WHEN `pt`.`group` = 'orator' THEN 'Orateurs'
        WHEN `pt`.`group` = 'industry' THEN 'Industriels'
    END AS `participation_type_group_display`,
    JSON_UNQUOTE(JSON_EXTRACT(`pt`.`name`, '$.fr')) AS `participation_type`,
    (SELECT COUNT(0)
     FROM `orders` `o`
     WHERE `o`.`event_id` = `ec`.`event_id`
       AND `o`.`client_id` = `ec`.`user_id`
       AND `o`.`client_type` = 'contact') AS `nb_orders`,
    (SELECT COUNT(0)
     FROM (`event_sellable_service`
           JOIN `event_contact_sellable_service_choosables`
           ON (`event_sellable_service`.`id` = `event_contact_sellable_service_choosables`.`choosable_id`))
     WHERE `ec`.`id` = `event_contact_sellable_service_choosables`.`event_contact_id`
       AND `event_sellable_service`.`deleted_at` IS NULL) +
    (SELECT COUNT(0)
     FROM `event_program_session_moderators`
     WHERE `ec`.`id` = `event_program_session_moderators`.`events_contacts_id`) +
    (SELECT COUNT(0)
     FROM `event_program_intervention_orators`
     WHERE `ec`.`id` = `event_program_intervention_orators`.`events_contacts_id`) +
    (SELECT COUNT(0) FROM `event_transports` WHERE `ec`.`id` = `event_transports`.`events_contacts_id`) +
    (SELECT COUNT(0)
     FROM `orders`
     WHERE `ec`.`user_id` = `orders`.`client_id`
       AND `orders`.`client_type` = 'contact'
       AND `orders`.`event_id` = `ec`.`event_id`) AS `has_something`,
    -- Add the new column `has_paid_grant_deposit`
    CASE
        WHEN `ec`.`grant_deposit_not_needed` IS NOT NULL THEN 1
        WHEN EXISTS (
            SELECT 1
            FROM `event_deposits` `ed`
            WHERE `ed`.`event_contact_id` = `ec`.`id`
              AND `ed`.`shoppable_type` = 'grantdeposit'
              AND `ed`.`status` IN ('paid', 'billed')
        ) THEN 1
        ELSE NULL
    END AS `has_paid_grant_deposit`
FROM (((((((((((`events_contacts` `ec`
JOIN `users` `u` ON (`u`.`id` = `ec`.`user_id`))
JOIN `events` `e` ON (`e`.`id` = `ec`.`event_id`))
JOIN `account_profile` `ap` ON (`u`.`id` = `ap`.`user_id`))
LEFT JOIN `participation_types` `pt` ON (`pt`.`id` = `ec`.`participation_type_id`))
LEFT JOIN `dictionnary_entries` `d` ON (`ap`.`domain_id` = `d`.`id`))
LEFT JOIN `dictionnary_entries` `de` ON (`ap`.`profession_id` = `de`.`id`))
LEFT JOIN `main_account_address_view` `a` ON (`u`.`id` = `a`.`user_id`))
LEFT JOIN `countries` `c` ON (`a`.`country_code` = `c`.`code`))
LEFT JOIN `event_group_contacts` `egc` ON (`ec`.`user_id` = `egc`.`user_id`))
LEFT JOIN `event_groups` `eg` ON (`egc`.`event_group_id` = `eg`.`id` AND `ec`.`event_id` = `eg`.`event_id`))
LEFT JOIN `groups` `g` ON (`eg`.`group_id` = `g`.`id`))
WHERE `g`.`deleted_at` IS NULL
  AND `u`.`deleted_at` IS NULL
GROUP BY `ec`.`id`, `e`.`id`, `u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`,
         JSON_UNQUOTE(JSON_EXTRACT(`d`.`name`, '$.fr')),
         CASE
             WHEN `ap`.`account_type` = 'company' THEN 'Sociétés'
             WHEN `ap`.`account_type` = 'medical' THEN 'Professionnels de santé'
             WHEN `ap`.`account_type` = 'other' THEN 'Autres'
         END, `ap`.`company_name`, `a`.`locality`,
         JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')), JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')),
         `ec`.`created_at`, `pt`.`group`,
         CASE
             WHEN `pt`.`group` IS NULL THEN '-'
             WHEN `pt`.`group` = 'congress' THEN 'Congressistes'
             WHEN `pt`.`group` = 'orator' THEN 'Orateurs'
             WHEN `pt`.`group` = 'industry' THEN 'Industriels'
         END, JSON_UNQUOTE(JSON_EXTRACT(`pt`.`name`, '$.fr'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement("CREATE OR REPLACE VIEW event_contact_view AS
select `ec`.`id` AS `id`,`e`.`id` AS `event_id`,`u`.`id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `domain`,case when `ap`.`account_type` = 'company' then 'Sociétés' when `ap`.`account_type` = 'medical' then 'Professionnels de santé' when `ap`.`account_type` = 'other' then 'Autres' end AS `account_type_display`,`ap`.`company_name` AS `company_name`,`a`.`locality` AS `locality`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,json_unquote(json_extract(`de`.`name`,'$.fr')) AS `fonction`,group_concat(distinct `g`.`name` separator ', ') AS `group`,concat(',',group_concat(distinct `g`.`id` separator ','),',') AS `group_ids`,`ec`.`created_at` AS `created_at`,`ec`.`registration_type` AS `registration_type`,`ec`.`order_cancellation` AS `order_cancellation`,`ec`.`is_pec_eligible` AS `pec_eligible`,`ec`.`pec_enabled` AS `pec_enabled`,case when `ec`.`pec_enabled` = 1 then 'Activée' else NULL end AS `pec_enabled_display`,case when `ec`.`is_pec_eligible` = 1 then 'Eligible' else NULL end AS `pec_eligible_display`,`pt`.`group` AS `participation_type_group`,case when `pt`.`group` is null then '-' when `pt`.`group` = 'congress' then 'Congressistes' when `pt`.`group` = 'orator' then 'Orateurs' when `pt`.`group` = 'industry' then 'Industriels' end AS `participation_type_group_display`,json_unquote(json_extract(`pt`.`name`,'$.fr')) AS `participation_type`,(select count(0) from `orders` `o` where `o`.`event_id` = `ec`.`event_id` and `o`.`client_id` = `ec`.`user_id` and `o`.`client_type` = 'contact') AS `nb_orders`,(select count(0) from (`event_sellable_service` join `event_contact_sellable_service_choosables` on(`event_sellable_service`.`id` = `event_contact_sellable_service_choosables`.`choosable_id`)) where `ec`.`id` = `event_contact_sellable_service_choosables`.`event_contact_id` and `event_sellable_service`.`deleted_at` is null) + (select count(0) from `event_program_session_moderators` where `ec`.`id` = `event_program_session_moderators`.`events_contacts_id`) + (select count(0) from `event_program_intervention_orators` where `ec`.`id` = `event_program_intervention_orators`.`events_contacts_id`) + (select count(0) from `event_transports` where `ec`.`id` = `event_transports`.`events_contacts_id`) + (select count(0) from `orders` where `ec`.`user_id` = `orders`.`client_id` and `orders`.`client_type` = 'contact' and `orders`.`event_id` = `ec`.`event_id`) AS `has_something` from (((((((((((`events_contacts` `ec` join `users` `u` on(`u`.`id` = `ec`.`user_id`)) join `events` `e` on(`e`.`id` = `ec`.`event_id`)) join `account_profile` `ap` on(`u`.`id` = `ap`.`user_id`)) left join `participation_types` `pt` on(`pt`.`id` = `ec`.`participation_type_id`)) left join `dictionnary_entries` `d` on(`ap`.`domain_id` = `d`.`id`)) left join `dictionnary_entries` `de` on(`ap`.`profession_id` = `de`.`id`)) left join `main_account_address_view` `a` on(`u`.`id` = `a`.`user_id`)) left join `countries` `c` on(`a`.`country_code` = `c`.`code`)) left join `event_group_contacts` `egc` on(`ec`.`user_id` = `egc`.`user_id`)) left join `event_groups` `eg` on(`egc`.`event_group_id` = `eg`.`id` and `ec`.`event_id` = `eg`.`event_id`)) left join `groups` `g` on(`eg`.`group_id` = `g`.`id`)) where `g`.`deleted_at` is null and `u`.`deleted_at` is null group by `ec`.`id`,`e`.`id`,`u`.`id`,`u`.`first_name`,`u`.`last_name`,`u`.`email`,json_unquote(json_extract(`d`.`name`,'$.fr')),case when `ap`.`account_type` = 'company' then 'Sociétés' when `ap`.`account_type` = 'medical' then 'Professionnels de santé' when `ap`.`account_type` = 'other' then 'Autres' end,`ap`.`company_name`,`a`.`locality`,json_unquote(json_extract(`c`.`name`,'$.fr')),json_unquote(json_extract(`de`.`name`,'$.fr')),`ec`.`created_at`,`pt`.`group`,case when `pt`.`group` is null then '-' when `pt`.`group` = 'congress' then 'Congressistes' when `pt`.`group` = 'orator' then 'Orateurs' when `pt`.`group` = 'industry' then 'Industriels' end,json_unquote(json_extract(`pt`.`name`,'$.fr'))
        ");
    }
};
