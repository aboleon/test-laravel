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
        DB::statement("CREATE OR  REPLACE VIEW event_contact_view AS
select `ec`.`id`                                                          AS `id`,
       `ec`.`uuid`                                                        AS `uuid`,
       `e`.`id`                                                           AS `event_id`,
       `u`.`id`                                                           AS `user_id`,
       `u`.`first_name`                                                   AS `first_name`,
       `u`.`last_name`                                                    AS `last_name`,
       `u`.`email`                                                        AS `email`,
       json_unquote(json_extract(`d`.`name`, '$.fr'))                     AS `domain`,
       case
           when `ap`.`account_type` = 'company' then 'Sociétés'
           when `ap`.`account_type` = 'medical' then 'Professionnels de santé'
           when `ap`.`account_type` = 'other' then 'Autres' end           AS `account_type_display`,
       `ap`.`company_name`                                                AS `company_name`,
       `a`.`locality`                                                     AS `locality`,
       json_unquote(json_extract(`c`.`name`, '$.fr'))                     AS `country`,
       json_unquote(json_extract(`de`.`name`, '$.fr'))                    AS `fonction`,
       group_concat(distinct `g`.`name` separator ', ')                   AS `group`,
       concat(',', group_concat(distinct `g`.`id` separator ','), ',')    AS `group_ids`,
       `ec`.`created_at`                                                  AS `created_at`,
       `ec`.`registration_type`                                           AS `registration_type`,
       `ec`.`order_cancellation`                                          AS `order_cancellation`,
       `ec`.`is_pec_eligible`                                             AS `pec_eligible`,
       `ec`.`pec_enabled`                                                 AS `pec_enabled`,
       case when `ec`.`pec_enabled` = 1 then 'Activée' else NULL end      AS `pec_enabled_display`,
       case when `ec`.`is_pec_eligible` = 1 then 'Eligible' else NULL end AS `pec_eligible_display`,
       `pt`.`group`                                                       AS `participation_type_group`,
       case
           when `pt`.`group` is null then '-'
           when `pt`.`group` = 'congress' then 'Congressistes'
           when `pt`.`group` = 'orator' then 'Orateurs'
           when `pt`.`group` = 'industry' then 'Industriels' end          AS `participation_type_group_display`,
       json_unquote(json_extract(`pt`.`name`, '$.fr'))                    AS `participation_type`,
       (select count(0)
        from `orders` `o`
        where `o`.`event_id` = `ec`.`event_id`
          and `o`.`client_id` = `ec`.`user_id`
          and `o`.`type` = 'order'
          and `o`.`marker` = 'normal'
          and `o`.`client_type` = 'contact')                              AS `nb_orders`,
       (select count(0)
        from (`event_sellable_service` join `event_contact_sellable_service_choosables`
              on (`event_sellable_service`.`id` = `event_contact_sellable_service_choosables`.`choosable_id`))
        where `ec`.`id` = `event_contact_sellable_service_choosables`.`event_contact_id`
          and `event_sellable_service`.`deleted_at` is null) + (select count(0)
                                                                from `event_program_session_moderators`
                                                                where `ec`.`id` = `event_program_session_moderators`.`events_contacts_id`) +
       (select count(0)
        from `event_program_intervention_orators`
        where `ec`.`id` = `event_program_intervention_orators`.`events_contacts_id`) +
       (select count(0) from `event_transports` where `ec`.`id` = `event_transports`.`events_contacts_id`) +
       (select count(0)
        from `orders`
        where `ec`.`user_id` = `orders`.`client_id`
          and `orders`.`client_type` = 'contact'
          and `orders`.`event_id` = `ec`.`event_id`)                      AS `has_something`,
       case
           when `ec`.`grant_deposit_not_needed` is not null then 1
           when exists(select 1
                       from `event_deposits` `ed`
                       where `ed`.`event_contact_id` = `ec`.`id`
                         and `ed`.`shoppable_type` = 'grantdeposit'
                         and `ed`.`status` in ('paid', 'billed')
                       limit 1) then 1
           else NULL end                                                  AS `has_paid_grant_deposit`,
       case
           when exists(select 1
                       from `event_deposits` `ed`
                       where `ed`.`event_contact_id` = `ec`.`id`
                         and `ed`.`shoppable_type` <> 'grantdeposit'
                         and `ed`.`status` in ('paid', 'billed')
                       limit 1) then 1
           else NULL end                                                  AS `has_paid_service_deposit`
from (((((((((((`events_contacts` `ec` join `users` `u` on (`u`.`id` = `ec`.`user_id`)) join `events` `e`
               on (`e`.`id` = `ec`.`event_id`)) join `account_profile` `ap`
              on (`u`.`id` = `ap`.`user_id`)) left join `participation_types` `pt`
             on (`pt`.`id` = `ec`.`participation_type_id`)) left join `dictionnary_entries` `d`
            on (`ap`.`domain_id` = `d`.`id`)) left join `dictionnary_entries` `de`
           on (`ap`.`profession_id` = `de`.`id`)) left join `main_account_address_view` `a`
          on (`u`.`id` = `a`.`user_id`)) left join `countries` `c`
         on (`a`.`country_code` = `c`.`code`)) left join `event_group_contacts` `egc`
        on (`ec`.`user_id` = `egc`.`user_id`)) left join `event_groups` `eg`
       on (`egc`.`event_group_id` = `eg`.`id` and `ec`.`event_id` = `eg`.`event_id`)) left join `groups` `g`
      on (`eg`.`group_id` = `g`.`id` and `g`.`deleted_at` is null))  -- MOVED the condition here
where `u`.`deleted_at` is null  -- REMOVED the g.deleted_at condition from WHERE
group by `ec`.`id`, `e`.`id`, `u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`,
         json_unquote(json_extract(`d`.`name`, '$.fr')),
         case
             when `ap`.`account_type` = 'company' then 'Sociétés'
             when `ap`.`account_type` = 'medical' then 'Professionnels de santé'
             when `ap`.`account_type` = 'other' then 'Autres' end, `ap`.`company_name`, `a`.`locality`,
         json_unquote(json_extract(`c`.`name`, '$.fr')), json_unquote(json_extract(`de`.`name`, '$.fr')),
         `ec`.`created_at`, `pt`.`group`,
         case
             when `pt`.`group` is null then '-'
             when `pt`.`group` = 'congress' then 'Congressistes'
             when `pt`.`group` = 'orator' then 'Orateurs'
             when `pt`.`group` = 'industry' then 'Industriels' end, json_unquote(json_extract(`pt`.`name`, '$.fr'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR  REPLACE VIEW event_contact_view AS
select `ec`.`id` AS `id`,`ec`.`uuid` AS `uuid`,`e`.`id` AS `event_id`,`u`.`id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `domain`,case when `ap`.`account_type` = 'company' then 'Sociétés' when `ap`.`account_type` = 'medical' then 'Professionnels de santé' when `ap`.`account_type` = 'other' then 'Autres' end AS `account_type_display`,`ap`.`company_name` AS `company_name`,`a`.`locality` AS `locality`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,json_unquote(json_extract(`de`.`name`,'$.fr')) AS `fonction`,group_concat(distinct `g`.`name` separator ', ') AS `group`,concat(',',group_concat(distinct `g`.`id` separator ','),',') AS `group_ids`,`ec`.`created_at` AS `created_at`,`ec`.`registration_type` AS `registration_type`,`ec`.`order_cancellation` AS `order_cancellation`,`ec`.`is_pec_eligible` AS `pec_eligible`,`ec`.`pec_enabled` AS `pec_enabled`,case when `ec`.`pec_enabled` = 1 then 'Activée' else NULL end AS `pec_enabled_display`,case when `ec`.`is_pec_eligible` = 1 then 'Eligible' else NULL end AS `pec_eligible_display`,`pt`.`group` AS `participation_type_group`,case when `pt`.`group` is null then '-' when `pt`.`group` = 'congress' then 'Congressistes' when `pt`.`group` = 'orator' then 'Orateurs' when `pt`.`group` = 'industry' then 'Industriels' end AS `participation_type_group_display`,json_unquote(json_extract(`pt`.`name`,'$.fr')) AS `participation_type`,(select count(0) from `orders` `o` where `o`.`event_id` = `ec`.`event_id` and `o`.`client_id` = `ec`.`user_id` and `o`.`type` = 'order' and `o`.`marker` = 'normal' and `o`.`client_type` = 'contact') AS `nb_orders`,(select count(0) from (`event_sellable_service` join `event_contact_sellable_service_choosables` on(`event_sellable_service`.`id` = `event_contact_sellable_service_choosables`.`choosable_id`)) where `ec`.`id` = `event_contact_sellable_service_choosables`.`event_contact_id` and `event_sellable_service`.`deleted_at` is null) + (select count(0) from `event_program_session_moderators` where `ec`.`id` = `event_program_session_moderators`.`events_contacts_id`) + (select count(0) from `event_program_intervention_orators` where `ec`.`id` = `event_program_intervention_orators`.`events_contacts_id`) + (select count(0) from `event_transports` where `ec`.`id` = `event_transports`.`events_contacts_id`) + (select count(0) from `orders` where `ec`.`user_id` = `orders`.`client_id` and `orders`.`client_type` = 'contact' and `orders`.`event_id` = `ec`.`event_id`) AS `has_something`,case when `ec`.`grant_deposit_not_needed` is not null then 1 when exists(select 1 from `event_deposits` `ed` where `ed`.`event_contact_id` = `ec`.`id` and `ed`.`shoppable_type` = 'grantdeposit' and `ed`.`status` in ('paid','billed') limit 1) then 1 else NULL end AS `has_paid_grant_deposit`,case when exists(select 1 from `event_deposits` `ed` where `ed`.`event_contact_id` = `ec`.`id` and `ed`.`shoppable_type` <> 'grantdeposit' and `ed`.`status` in ('paid','billed') limit 1) then 1 else NULL end AS `has_paid_service_deposit` from (((((((((((`events_contacts` `ec` join `users` `u` on(`u`.`id` = `ec`.`user_id`)) join `events` `e` on(`e`.`id` = `ec`.`event_id`)) join `account_profile` `ap` on(`u`.`id` = `ap`.`user_id`)) left join `participation_types` `pt` on(`pt`.`id` = `ec`.`participation_type_id`)) left join `dictionnary_entries` `d` on(`ap`.`domain_id` = `d`.`id`)) left join `dictionnary_entries` `de` on(`ap`.`profession_id` = `de`.`id`)) left join `main_account_address_view` `a` on(`u`.`id` = `a`.`user_id`)) left join `countries` `c` on(`a`.`country_code` = `c`.`code`)) left join `event_group_contacts` `egc` on(`ec`.`user_id` = `egc`.`user_id`)) left join `event_groups` `eg` on(`egc`.`event_group_id` = `eg`.`id` and `ec`.`event_id` = `eg`.`event_id`)) left join `groups` `g` on(`eg`.`group_id` = `g`.`id`)) where `g`.`deleted_at` is null and `u`.`deleted_at` is null group by `ec`.`id`,`e`.`id`,`u`.`id`,`u`.`first_name`,`u`.`last_name`,`u`.`email`,json_unquote(json_extract(`d`.`name`,'$.fr')),case when `ap`.`account_type` = 'company' then 'Sociétés' when `ap`.`account_type` = 'medical' then 'Professionnels de santé' when `ap`.`account_type` = 'other' then 'Autres' end,`ap`.`company_name`,`a`.`locality`,json_unquote(json_extract(`c`.`name`,'$.fr')),json_unquote(json_extract(`de`.`name`,'$.fr')),`ec`.`created_at`,`pt`.`group`,case when `pt`.`group` is null then '-' when `pt`.`group` = 'congress' then 'Congressistes' when `pt`.`group` = 'orator' then 'Orateurs' when `pt`.`group` = 'industry' then 'Industriels' end,json_unquote(json_extract(`pt`.`name`,'$.fr'))
        ");
    }
};
