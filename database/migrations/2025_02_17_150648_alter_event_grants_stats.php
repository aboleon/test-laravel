<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW event_grant_stats AS
        SELECT
    `pd`.`grant_id` AS `grant_id`,
    `pd`.`order_id` AS `order_id`,
    `o`.`event_id` AS `event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `participant`,
    FORMAT(SUM(`pd`.`total_net`) / 100.0, 2) AS `total_net`,
    FORMAT(SUM(`pd`.`total_vat`) / 100.0, 2) AS `total_vat`,
    FORMAT(SUM(`pd`.`total_net` + `pd`.`total_vat`) / 100.0, 2) AS `total`,
    CASE
        WHEN `pd`.`type` = 'uncategorized' THEN 'Non catégorisé'
        WHEN `pd`.`type` = 'service' THEN 'Prestation'
        WHEN `pd`.`type` = 'accommodation' THEN 'Hebergement'
        WHEN `pd`.`type` = 'taxroom' THEN 'Frais de dossier hébergement'
        WHEN `pd`.`type` = 'transport' THEN 'Transport'
        WHEN `pd`.`type` = 'processing_fee' THEN 'Frais de dossier'
    END AS `type`,
    CASE
        WHEN `pd`.`type` = 'service' THEN JSON_UNQUOTE(JSON_EXTRACT(`ess`.`title`, '$.fr'))
        WHEN `pd`.`type` IN ('accommodation', 'taxroom') THEN GROUP_CONCAT(DISTINCT CONCAT(
            JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')), ', ', JSON_UNQUOTE(JSON_EXTRACT(`eagr`.`name`, '$.fr')),
            ', ', `h`.`name`) SEPARATOR ',')
    END AS `shoppable`,
    `aa`.`locality` AS `locality`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `country`
FROM `pec_distribution` `pd`
    JOIN `orders` `o` ON (`pd`.`order_id` = `o`.`id`)
    JOIN `events_contacts` `ec` ON (`pd`.`event_contact_id` = `ec`.`id`)
    JOIN `users` `u` ON (`ec`.`user_id` = `u`.`id`)
    LEFT JOIN `event_sellable_service` `ess` ON (`pd`.`shoppable_id` = `ess`.`id` AND `pd`.`type` = 'service')
    LEFT JOIN `event_accommodation_room` `ear` ON (`pd`.`shoppable_id` = `ear`.`id` AND `pd`.`type` IN ('accommodation', 'taxroom'))
    LEFT JOIN `event_accommodation_room_groups` `eagr` ON (`ear`.`room_group_id` = `eagr`.`id`)
    LEFT JOIN `dictionnary_entries` `de` ON (`ear`.`room_id` = `de`.`id`)
    LEFT JOIN `event_accommodation` `ea` ON (`eagr`.`event_accommodation_id` = `ea`.`id`)
    LEFT JOIN `hotels` `h` ON (`ea`.`hotel_id` = `h`.`id`)
    JOIN `order_invoiceable` `oi` ON (`oi`.`order_id` = `o`.`id`)
    JOIN `account_address` `aa` ON (`aa`.`id` = `oi`.`address_id`)
    JOIN `countries` `c` ON (`c`.`code` = `aa`.`country_code`)
GROUP BY
    `pd`.`grant_id`,
    `pd`.`order_id`,
    `o`.`event_id`,
    `participant`,
    `type`,
    `ess`.`title`,
    `aa`.`locality`,
    `c`.`name`
HAVING
    SUM(`pd`.`total_net`) != 0",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW event_grant_stats AS
select `pd`.`grant_id` AS `grant_id`,`pd`.`order_id` AS `order_id`,`o`.`event_id` AS `event_id`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `participant`,format(sum(`pd`.`total_net`) / 100.0,2) AS `total_net`,format(sum(`pd`.`total_vat`) / 100.0,2) AS `total_vat`,format(sum(`pd`.`total_net` + `pd`.`total_vat`) / 100.0,2) AS `total`,case when `pd`.`type` = 'uncategorized' then 'Non catégorisé' when `pd`.`type` = 'service' then 'Prestation' when `pd`.`type` = 'accommodation' then 'Hebergement' when `pd`.`type` = 'taxroom' then 'Frais de dossier hébergement' when `pd`.`type` = 'transport' then 'Transport' when `pd`.`type` = 'processing_fee' then 'Frais de dossier' end AS `type`,case when `pd`.`type` = 'service' then json_unquote(json_extract(`ess`.`title`,'$.fr')) when `pd`.`type` in ('accommodation','taxroom') then group_concat(distinct concat(json_unquote(json_extract(`de`.`name`,'$.fr')),', ',json_unquote(json_extract(`eagr`.`name`,'$.fr')),', ',`h`.`name`) separator ',') end AS `shoppable` from (((((((((`pec_distribution` `pd` join `orders` `o` on(`pd`.`order_id` = `o`.`id`)) join `events_contacts` `ec` on(`pd`.`event_contact_id` = `ec`.`id`)) join `users` `u` on(`ec`.`user_id` = `u`.`id`)) left join `event_sellable_service` `ess` on(`pd`.`shoppable_id` = `ess`.`id` and `pd`.`type` = 'service')) left join `event_accommodation_room` `ear` on(`pd`.`shoppable_id` = `ear`.`id` and `pd`.`type` in ('accommodation','taxroom'))) left join `event_accommodation_room_groups` `eagr` on(`ear`.`room_group_id` = `eagr`.`id`)) left join `dictionnary_entries` `de` on(`ear`.`room_id` = `de`.`id`)) left join `event_accommodation` `ea` on(`eagr`.`event_accommodation_id` = `ea`.`id`)) left join `hotels` `h` on(`ea`.`hotel_id` = `h`.`id`)) group by `pd`.`grant_id`,`pd`.`order_id`,`o`.`event_id`,concat(`u`.`first_name`,' ',`u`.`last_name`),`pd`.`type`,`ess`.`title`
            ",
        );
    }
};
