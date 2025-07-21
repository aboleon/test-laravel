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
        DB::statement("CREATE OR REPLACE VIEW event_grant_stats AS
        SELECT
    `pd`.`id` AS `distribution_id`,
    `pd`.`grant_id` AS `grant_id`,
    `pd`.`order_id` AS `order_id`,
    `pd`.`type` AS `type_row`,
    `o`.`event_id` AS `event_id`,
    `pd`.`event_contact_id` AS `event_contact_id`,
    date_format(`pd`.`created_at`, '%d/%m/%Y') AS `created_at`,
    concat(`u`.`first_name`, ' ', `u`.`last_name`) AS `participant`,
    format(sum(coalesce(`pd`.`total_net`, 0)) / 100.0, 2) AS `total_net`,
    format(sum(coalesce(`pd`.`total_vat`, 0)) / 100.0, 2) AS `total_vat`,
    format(sum(
               CASE
                   WHEN `pd`.`type` = 'transport' THEN coalesce(`pd`.`unit_price`, 0)
                   ELSE coalesce(`pd`.`total_net`, 0) + coalesce(`pd`.`total_vat`, 0)
                   END) / 100.0, 2) AS `total`,
    CASE
        WHEN `pd`.`type` = 'uncategorized' THEN 'Non catégorisé'
        WHEN `pd`.`type` = 'service' THEN 'Prestation'
        WHEN `pd`.`type` = 'accommodation' THEN 'Hebergement'
        WHEN `pd`.`type` = 'taxroom' THEN 'Frais de dossier hébergement'
        WHEN `pd`.`type` = 'transport' THEN 'Transport'
        WHEN `pd`.`type` = 'processing_fee' THEN 'Frais de dossier'
        END AS `type`,
    CASE
        WHEN `pd`.`type` = 'service' THEN json_unquote(json_extract(`ess`.`title`, '$.fr'))
        WHEN `pd`.`type` IN ('accommodation', 'taxroom') THEN
            group_concat(distinct concat(
                json_unquote(json_extract(`de`.`name`, '$.fr')), ', ',
                json_unquote(json_extract(`eagr`.`name`, '$.fr')), ', ',
                `h`.`name`) SEPARATOR ',')
        WHEN `pd`.`type` = 'transport' THEN
            CASE
                WHEN `pd`.`shoppable_id` = 1 THEN 'Divine Id'
                WHEN `pd`.`shoppable_id` = 2 THEN 'Participant'
                WHEN `pd`.`shoppable_id` = 3 THEN 'Non demandé'
                ELSE NULL
                END
        END AS `shoppable`,
    `aa`.`locality` AS `locality`,
    json_unquote(json_extract(`c`.`name`, '$.fr')) AS `country`,
    `eg`.`amount_type` AS `grant_type`,
    json_unquote(json_extract(`eg`.`title`, '$.fr')) AS `grant_title`
FROM `pec_distribution` `pd`
         LEFT JOIN `orders` `o` ON (`pd`.`order_id` = `o`.`id`)
         LEFT JOIN `events_contacts` `ec` ON (`pd`.`event_contact_id` = `ec`.`id`)
         LEFT JOIN `users` `u` ON (`ec`.`user_id` = `u`.`id`)
         LEFT JOIN `event_grant` `eg` ON (`pd`.`grant_id` = `eg`.`id`)
         LEFT JOIN `event_sellable_service` `ess` ON (`pd`.`shoppable_id` = `ess`.`id` AND `pd`.`type` = 'service')
         LEFT JOIN `event_accommodation_room` `ear` ON (`pd`.`shoppable_id` = `ear`.`id` AND `pd`.`type` IN ('accommodation', 'taxroom'))
         LEFT JOIN `event_accommodation_room_groups` `eagr` ON (`ear`.`room_group_id` = `eagr`.`id`)
         LEFT JOIN `dictionnary_entries` `de` ON (`ear`.`room_id` = `de`.`id`)
         LEFT JOIN `event_accommodation` `ea` ON (`eagr`.`event_accommodation_id` = `ea`.`id`)
         LEFT JOIN `hotels` `h` ON (`ea`.`hotel_id` = `h`.`id`)
         LEFT JOIN `order_invoiceable` `oi` ON (`oi`.`order_id` = `o`.`id`)
         LEFT JOIN account_address `aa`
                   ON `aa`.`user_id` = `ec`.`user_id`
                       AND (
                          `aa`.`billing` = 1
                              OR (SELECT COUNT(*) FROM account_address WHERE user_id = `ec`.`user_id` AND billing = 1) = 0
                          )
         LEFT JOIN `countries` `c` ON (`c`.`code` = `aa`.`country_code`)
GROUP BY `pd`.`grant_id`, `pd`.`order_id`, `o`.`event_id`, concat(`u`.`first_name`, ' ', `u`.`last_name`), `pd`.`type`,
         `ess`.`title`, `aa`.`locality`, `c`.`name`, `eg`.`amount_type`, `pd`.`shoppable_id`
HAVING sum(
           CASE
               WHEN `pd`.`type` = 'transport' THEN coalesce(`pd`.`unit_price`, 0)
               ELSE coalesce(`pd`.`total_net`, 0) + coalesce(`pd`.`total_vat`, 0)
               END) <> 0;

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_stats AS
        select `pd`.`id` AS `distribution_id`,`pd`.`grant_id` AS `grant_id`,`pd`.`order_id` AS `order_id`,`pd`.`type` AS `type_row`,`o`.`event_id` AS `event_id`,`pd`.`event_contact_id` AS `event_contact_id`,date_format(`pd`.`created_at`,'%d/%m/%Y') AS `created_at`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `participant`,format(sum(coalesce(`pd`.`total_net`,0)) / 100.0,2) AS `total_net`,format(sum(coalesce(`pd`.`total_vat`,0)) / 100.0,2) AS `total_vat`,format(sum(case when `pd`.`type` = 'transport' then coalesce(`pd`.`unit_price`,0) else coalesce(`pd`.`total_net`,0) + coalesce(`pd`.`total_vat`,0) end) / 100.0,2) AS `total`,case when `pd`.`type` = 'uncategorized' then 'Non catégorisé' when `pd`.`type` = 'service' then 'Prestation' when `pd`.`type` = 'accommodation' then 'Hebergement' when `pd`.`type` = 'taxroom' then 'Frais de dossier hébergement' when `pd`.`type` = 'transport' then 'Transport' when `pd`.`type` = 'processing_fee' then 'Frais de dossier' end AS `type`,case when `pd`.`type` = 'service' then json_unquote(json_extract(`ess`.`title`,'$.fr')) when `pd`.`type` in ('accommodation','taxroom') then group_concat(distinct concat(json_unquote(json_extract(`de`.`name`,'$.fr')),', ',json_unquote(json_extract(`eagr`.`name`,'$.fr')),', ',`h`.`name`) separator ',') when `pd`.`type` = 'transport' then case when `pd`.`shoppable_id` = 1 then 'Divine Id' when `pd`.`shoppable_id` = 2 then 'Participant' when `pd`.`shoppable_id` = 3 then 'Non demandé' else NULL end end AS `shoppable`,`aa`.`locality` AS `locality`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,`eg`.`amount_type` AS `grant_type`,json_unquote(json_extract(`eg`.`title`,'$.fr')) AS `grant_title` from (((((((((((((`pec_distribution` `pd` left join `orders` `o` on(`pd`.`order_id` = `o`.`id`)) left join `events_contacts` `ec` on(`pd`.`event_contact_id` = `ec`.`id`)) left join `users` `u` on(`ec`.`user_id` = `u`.`id`)) left join `event_grant` `eg` on(`pd`.`grant_id` = `eg`.`id`)) left join `event_sellable_service` `ess` on(`pd`.`shoppable_id` = `ess`.`id` and `pd`.`type` = 'service')) left join `event_accommodation_room` `ear` on(`pd`.`shoppable_id` = `ear`.`id` and `pd`.`type` in ('accommodation','taxroom'))) left join `event_accommodation_room_groups` `eagr` on(`ear`.`room_group_id` = `eagr`.`id`)) left join `dictionnary_entries` `de` on(`ear`.`room_id` = `de`.`id`)) left join `event_accommodation` `ea` on(`eagr`.`event_accommodation_id` = `ea`.`id`)) left join `hotels` `h` on(`ea`.`hotel_id` = `h`.`id`)) left join `order_invoiceable` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `account_address` `aa` on(`pd`.`type` = 'transport' and `aa`.`user_id` = `ec`.`user_id` and (`aa`.`billing` = 1 or `aa`.`billing` is null) or `pd`.`type` <> 'transport' and `aa`.`id` = `oi`.`address_id`)) left join `countries` `c` on(`c`.`code` = `aa`.`country_code`)) group by `pd`.`grant_id`,`pd`.`order_id`,`o`.`event_id`,concat(`u`.`first_name`,' ',`u`.`last_name`),`pd`.`type`,`ess`.`title`,`aa`.`locality`,`c`.`name`,`eg`.`amount_type`,`pd`.`shoppable_id` having sum(case when `pd`.`type` = 'transport' then coalesce(`pd`.`unit_price`,0) else coalesce(`pd`.`total_net`,0) + coalesce(`pd`.`total_vat`,0) end) <> 0");
    }
};
