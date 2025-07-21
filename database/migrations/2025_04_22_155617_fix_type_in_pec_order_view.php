<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW pec_order_view AS
            select `pd`.`id`                                                           AS `pec_distribution_id`,
       `pd`.`type`                                                         AS `pec_distribution_type`,
       `pd`.`grant_id`                                                     AS `grant_id`,
       `pd`.`order_id`                                                     AS `order_id`,
       `ec`.`event_id`                                                     AS `event_id`,
       `pd`.`event_contact_id`                                             AS `event_contact_id`,
       concat(`u`.`first_name`, ' ', `u`.`last_name`)                      AS `participant`,
       format(sum(case when `pd`.`type` = 'transport' then 0 else coalesce(`pd`.`total_net`, 0) end) / 100.0,
              2)                                                           AS `total_net`,
       format(sum(case when `pd`.`type` = 'transport' then 0 else coalesce(`pd`.`total_vat`, 0) end) / 100.0,
              2)                                                           AS `total_vat`,
       format(sum(case
                      when `pd`.`type` = 'transport' then coalesce(`pd`.`unit_price`, 0)
                      else coalesce(`pd`.`total_net`, 0) + coalesce(`pd`.`total_vat`, 0) end) / 100.0,
              2)                                                           AS `total`,
       case
           when `pd`.`type` = 'uncategorized' then 'Non catégorisé'
           when `pd`.`type` = 'service' then 'Prestation'
           when `pd`.`type` = 'accommodation' then 'Hebergement'
           when `pd`.`type` = 'taxroom' then 'Frais de dossier hébergement'
           when `pd`.`type` = 'transport' then 'Transport'
           when `pd`.`type` = 'processing_fee' then 'Frais de dossier' end AS `type`,
       case
           when `pd`.`type` = 'service' then json_unquote(json_extract(`ess`.`title`, '$.fr'))
           when `pd`.`type` in ('accommodation', 'taxroom') then group_concat(distinct concat(
               json_unquote(json_extract(`de`.`name`, '$.fr')), ', ', json_unquote(json_extract(`eagr`.`name`, '$.fr')),
               ', ', `h`.`name`) separator ', ')
           when `pd`.`type` = 'transport' then case
                                                   when `pd`.`shoppable_id` = 1 then 'Divine Id'
                                                   when `pd`.`shoppable_id` = 2 then 'Participant'
                                                   when `pd`.`shoppable_id` = 3 then 'Non demandé'
                                                   else NULL end end       AS `shoppable`,
       json_unquote(json_extract(`c`.`name`, '$.fr'))                      AS `country`,
       json_unquote(json_extract(`de_domain`.`name`, '$.fr'))              AS `domain`,
       json_unquote(json_extract(`eg`.`title`, '$.fr'))                    AS `grant`
from (((((((((((((`pec_distribution` `pd` left join `events_contacts` `ec`
                  on (`pd`.`event_contact_id` = `ec`.`id`)) left join `users` `u`
                 on (`ec`.`user_id` = `u`.`id`)) left join `event_sellable_service` `ess`
                on (`pd`.`shoppable_id` = `ess`.`id` and `pd`.`type` = 'service')) left join `event_accommodation_room` `ear`
               on (`pd`.`shoppable_id` = `ear`.`id` and
                   `pd`.`type` in ('accommodation', 'taxroom'))) left join `event_accommodation_room_groups` `eagr`
              on (`ear`.`room_group_id` = `eagr`.`id`)) left join `dictionnary_entries` `de`
             on (`ear`.`room_id` = `de`.`id`)) left join `event_accommodation` `ea`
            on (`eagr`.`event_accommodation_id` = `ea`.`id`)) left join `hotels` `h`
           on (`ea`.`hotel_id` = `h`.`id`)) left join `event_grant` `eg`
          on (`pd`.`grant_id` = `eg`.`id`)) left join (select distinct `account_address`.`user_id`      AS `user_id`,
                                                                       `account_address`.`country_code` AS `country_code`
                                                       from `account_address`) `aa`
         on (`u`.`id` = `aa`.`user_id`)) left join `countries` `c`
        on (`aa`.`country_code` = `c`.`code`)) left join (select distinct `account_profile`.`user_id`   AS `user_id`,
                                                                          `account_profile`.`domain_id` AS `domain_id`
                                                          from `account_profile`) `ap`
       on (`u`.`id` = `ap`.`user_id`)) left join `dictionnary_entries` `de_domain`
      on (`ap`.`domain_id` = `de_domain`.`id`))
group by `pd`.`id`, `pd`.`type`, `pd`.`grant_id`, `pd`.`order_id`, `ec`.`event_id`,
         concat(`u`.`first_name`, ' ', `u`.`last_name`), `pd`.`shoppable_id`
having sum(case
               when `pd`.`type` = 'transport' then coalesce(`pd`.`unit_price`, 0)
               else coalesce(`pd`.`total_net`, 0) + coalesce(`pd`.`total_vat`, 0) end) <> 0",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW pec_order_view AS

select `pd`.`id` AS `pec_distribution_id`,`pd`.`type` AS `pec_distribution_type`,`pd`.`grant_id` AS `grant_id`,`pd`.`order_id` AS `order_id`,`ec`.`event_id` AS `event_id`,`pd`.`event_contact_id` AS `event_contact_id`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `participant`,format(sum(case when `pd`.`type` = 'transport' then 0 else coalesce(`pd`.`total_net`,0) end) / 100.0,2) AS `total_net`,format(sum(case when `pd`.`type` = 'transport' then 0 else coalesce(`pd`.`total_vat`,0) end) / 100.0,2) AS `total_vat`,format(sum(case when `pd`.`type` = 'transport' then coalesce(`pd`.`unit_price`,0) else coalesce(`pd`.`total_net`,0) + coalesce(`pd`.`total_vat`,0) end) / 100.0,2) AS `total`,case when `pd`.`type` = 'transport' then 'Transport' when `pd`.`type` = 'service' then 'Prestation' when `pd`.`type` = 'accommodation' then 'Hebergement' else 'Other' end AS `type`,case when `pd`.`type` = 'service' then json_unquote(json_extract(`ess`.`title`,'$.fr')) when `pd`.`type` in ('accommodation','taxroom') then group_concat(distinct concat(json_unquote(json_extract(`de`.`name`,'$.fr')),', ',json_unquote(json_extract(`eagr`.`name`,'$.fr')),', ',`h`.`name`) separator ', ') when `pd`.`type` = 'transport' then case when `pd`.`shoppable_id` = 1 then 'Divine Id' when `pd`.`shoppable_id` = 2 then 'Participant' when `pd`.`shoppable_id` = 3 then 'Non demandé' else NULL end end AS `shoppable`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,json_unquote(json_extract(`de_domain`.`name`,'$.fr')) AS `domain`,json_unquote(json_extract(`eg`.`title`,'$.fr')) AS `grant` from (((((((((((((`pec_distribution` `pd` left join `events_contacts` `ec` on(`pd`.`event_contact_id` = `ec`.`id`)) left join `users` `u` on(`ec`.`user_id` = `u`.`id`)) left join `event_sellable_service` `ess` on(`pd`.`shoppable_id` = `ess`.`id` and `pd`.`type` = 'service')) left join `event_accommodation_room` `ear` on(`pd`.`shoppable_id` = `ear`.`id` and `pd`.`type` in ('accommodation','taxroom'))) left join `event_accommodation_room_groups` `eagr` on(`ear`.`room_group_id` = `eagr`.`id`)) left join `dictionnary_entries` `de` on(`ear`.`room_id` = `de`.`id`)) left join `event_accommodation` `ea` on(`eagr`.`event_accommodation_id` = `ea`.`id`)) left join `hotels` `h` on(`ea`.`hotel_id` = `h`.`id`)) left join `event_grant` `eg` on(`pd`.`grant_id` = `eg`.`id`)) left join (select distinct `account_address`.`user_id` AS `user_id`,`account_address`.`country_code` AS `country_code` from `account_address`) `aa` on(`u`.`id` = `aa`.`user_id`)) left join `countries` `c` on(`aa`.`country_code` = `c`.`code`)) left join (select distinct `account_profile`.`user_id` AS `user_id`,`account_profile`.`domain_id` AS `domain_id` from `account_profile`) `ap` on(`u`.`id` = `ap`.`user_id`)) left join `dictionnary_entries` `de_domain` on(`ap`.`domain_id` = `de_domain`.`id`)) group by `pd`.`id`,`pd`.`type`,`pd`.`grant_id`,`pd`.`order_id`,`ec`.`event_id`,concat(`u`.`first_name`,' ',`u`.`last_name`),`pd`.`shoppable_id` having sum(case when `pd`.`type` = 'transport' then coalesce(`pd`.`unit_price`,0) else coalesce(`pd`.`total_net`,0) + coalesce(`pd`.`total_vat`,0) end) <> 0
        ",
        );
    }
};
