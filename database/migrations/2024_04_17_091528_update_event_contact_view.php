<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_contact_view AS
        select 
       `ec`.`id`                                                        AS `id`,
       `e`.`id`                                                         AS `event_id`,
       `u`.`id`                                                         AS `user_id`,
       `u`.`first_name`                                                 AS `first_name`,
       `u`.`last_name`                                                  AS `last_name`,
       `u`.`email`                                                      AS `email`,
       json_unquote(json_extract(`d`.`name`, '$.fr'))                   AS `domain`,
       (case
            when (`ap`.`account_type` = 'company') then 'Sociétés'
            when (`ap`.`account_type` = 'medical') then 'Professionnels de santé'
            when (`ap`.`account_type` = 'other') then 'Autres' end)     AS `account_type_display`,
       `ap`.`company_name`                                              AS `company_name`,
       `a`.`locality`                                                   AS `locality`,
       json_unquote(json_extract(`c`.`name`, '$.fr'))                   AS `country`,
       json_unquote(json_extract(`de`.`name`, '$.fr'))                  AS `fonction`,
       group_concat(`g`.`name` separator ', ')                          AS `group`,
       concat(',', group_concat(`g`.`id` separator ','), ',')           AS `group_ids`,
       `ec`.`created_at`                                                AS `created_at`,
       `ec`.`registration_type`                                         AS `registration_type`,
       `ec`.`order_cancellation`                                        AS `order_cancellation`,
       `ec`.`last_grant_id`                                             AS `last_grant_id`,
       CASE 
              WHEN 
                `ec`.`is_pec_eligible` = 1 AND `ec`.`pec_enabled` = 1 
              THEN '2'
              WHEN 
                `ec`.`is_pec_eligible` = 1 AND `ec`.`pec_enabled` IS NULL 
              THEN '1'
              ELSE NULL
       END                                                              AS `pec_status`,
       CASE 
              WHEN 
                `ec`.`is_pec_eligible` = 1 AND `ec`.`pec_enabled` = 1 
              THEN 'pec'
              WHEN 
                `ec`.`is_pec_eligible` = 1 AND `ec`.`pec_enabled` IS NULL 
              THEN 'eligible'
              ELSE NULL
       END                                                              AS `pec_status_display_fr`,
       `pt`.`group`                                                     AS `participation_type_group`,
       (case
            when (`pt`.`group` is null) then '-'
            when (`pt`.`group` = 'congress') then 'Congressistes'
            when (`pt`.`group` = 'orator') then 'Orateurs'
            when (`pt`.`group` = 'industry') then 'Industriels' end)    AS `participation_type_group_display`,
       json_unquote(json_extract(`pt`.`name`, '$.fr'))                  AS `participation_type`,
       (select count(0)
        from `orders` `o`
        where ((`o`.`event_id` = `ec`.`event_id`) and (`o`.`client_id` = `ec`.`user_id`) and
               (`o`.`client_type` = 'contact')))                        AS `nb_orders`,
       (
         (select count(*) from `event_sellable_service`
          inner join `event_contact_sellable_service_choosables`
          on `event_sellable_service`.`id` = `event_contact_sellable_service_choosables`.`choosable_id`
          where `ec`.`id` = `event_contact_sellable_service_choosables`.`event_contact_id`
          and `event_sellable_service`.`deleted_at` is null)
         +
         (select count(*) from `event_program_session_moderators`
          where `ec`.`id` = `event_program_session_moderators`.`events_contacts_id`)
         +
         (select count(*) from `event_program_intervention_orators`
          where `ec`.`id` = `event_program_intervention_orators`.`events_contacts_id`)
         +
         (select count(*) from `event_transports`
          where `ec`.`id` = `event_transports`.`events_contacts_id`)
         +
         (select count(*) from `orders`
          where `ec`.`user_id` = `orders`.`client_id`
          and `client_type` = 'contact'
          and `event_id` = `ec`.`event_id`)
       )                                                                 AS `has_something`
from (((((((((((`events_contacts` `ec` join `users` `u` on ((`u`.`id` = `ec`.`user_id`))) join `events` `e`
             on ((`e`.`id` = `ec`.`event_id`))) join `account_profile` `ap`
            on ((`u`.`id` = `ap`.`user_id`))) left join `participation_types` `pt`
           on ((`pt`.`id` = `ec`.`participation_type_id`))) left join `dictionnary_entries` `d`
          on ((`ap`.`domain_id` = `d`.`id`))) left join `dictionnary_entries` `de`
         on ((`ap`.`profession_id` = `de`.`id`))) left join `main_account_address_view` `a`
        on ((`u`.`id` = `a`.`user_id`))) left join `countries` `c`
       on ((`a`.`country_code` = `c`.`code`))) left join `event_group_contacts` `egc`
      on ((`u`.`id` = `egc`.`user_id`))) left join `event_groups` `eg`
     on ((`egc`.`event_group_id` = `eg`.`id`))) left join `groups` `g` on ((`eg`.`group_id` = `g`.`id`)))
where (`g`.`deleted_at` is null)
group by `ec`.`id`, `event_id`, `u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `domain`,
         `account_type_display`, `ap`.`company_name`, `a`.`locality`, `country`, `fonction`, `ec`.`created_at`,
         `participation_type_group`, `participation_type_group_display`, `participation_type`
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_contact_view AS
            select `ec`.`id`                                                     AS `id`,
       `e`.`id`                                                      AS `event_id`,
       `u`.`id`                                                      AS `user_id`,
       `u`.`first_name`                                              AS `first_name`,
       `u`.`last_name`                                               AS `last_name`,
       `u`.`email`                                                   AS `email`,
       json_unquote(json_extract(`d`.`name`, '$.fr'))                AS `domain`,
       (case
            when (`ap`.`account_type` = 'company') then 'Sociétés'
            when (`ap`.`account_type` = 'medical') then 'Professionnels de santé'
            when (`ap`.`account_type` = 'other') then 'Autres' end)  AS `account_type_display`,
       `ap`.`company_name`                                           AS `company_name`,
       `a`.`locality`                                                AS `locality`,
       json_unquote(json_extract(`c`.`name`, '$.fr'))                AS `country`,
       json_unquote(json_extract(`de`.`name`, '$.fr'))               AS `fonction`,
       group_concat(`g`.`name` separator ', ')                       AS `group`,
       concat(',', group_concat(`g`.`id` separator ','), ',')        AS `group_ids`,
       `ec`.`created_at`                                             AS `created_at`,
       `ec`.`registration_type`                                      AS `registration_type`,
       `ec`.`order_cancellation`                                     AS `order_cancellation`,
       `pt`.`group`                                                  AS `participation_type_group`,
       (case
            when (`pt`.`group` is null) then '-'
            when (`pt`.`group` = 'congress') then 'Congressistes'
            when (`pt`.`group` = 'orator') then 'Orateurs'
            when (`pt`.`group` = 'industry') then 'Industriels' end) AS `participation_type_group_display`,
       json_unquote(json_extract(`pt`.`name`, '$.fr'))               AS `participation_type`,
       (select count(0)
        from `orders` `o`
        where ((`o`.`event_id` = `ec`.`event_id`) and (`o`.`client_id` = `ec`.`user_id`) and
            (`o`.`client_type` = 'contact')))                     AS `nb_orders`
from (((((((((((`events_contacts` `ec` join `users` `u` on ((`u`.`id` = `ec`.`user_id`))) join `events` `e`
               on ((`e`.`id` = `ec`.`event_id`))) join `account_profile` `ap`
              on ((`u`.`id` = `ap`.`user_id`))) left join `participation_types` `pt`
             on ((`pt`.`id` = `ec`.`participation_type_id`))) left join `dictionnary_entries` `d`
            on ((`ap`.`domain_id` = `d`.`id`))) left join `dictionnary_entries` `de`
           on ((`ap`.`profession_id` = `de`.`id`))) left join `main_account_address_view` `a`
          on ((`u`.`id` = `a`.`user_id`))) left join `countries` `c`
         on ((`a`.`country_code` = `c`.`code`))) left join `event_group_contacts` `egc`
        on ((`u`.`id` = `egc`.`user_id`))) left join `event_groups` `eg`
       on ((`egc`.`event_group_id` = `eg`.`id`))) left join `groups` `g` on ((`eg`.`group_id` = `g`.`id`)))
where (`g`.`deleted_at` is null)
group by `ec`.`id`, `event_id`, `u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `domain`,
         `account_type_display`, `ap`.`company_name`, `a`.`locality`, `country`, `fonction`, `ec`.`created_at`,
         `participation_type_group`, `participation_type_group_display`, `participation_type`
        ;");
    }
};
