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
        DB::statement("CREATE OR REPLACE VIEW pec_order_view AS
SELECT
    `pd`.`type` AS `pec_distribution_type`,
    `pd`.`grant_id` AS `grant_id`,
    `pd`.`order_id` AS `order_id`,
    `o`.`event_id` AS `event_id`,
    `pd`.`event_contact_id` AS `event_contact_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `participant`,
    FORMAT(SUM(DISTINCT `pd`.`total_net`) / 100.0, 2) AS `total_net`,
    FORMAT(SUM(DISTINCT `pd`.`total_vat`) / 100.0, 2) AS `total_vat`,
    FORMAT(SUM(DISTINCT (`pd`.`total_net` + `pd`.`total_vat`)) / 100.0, 2) AS `total`,
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
        WHEN `pd`.`type` IN ('accommodation', 'taxroom') THEN (
            SELECT GROUP_CONCAT(DISTINCT CONCAT(
                JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')), ', ',
                JSON_UNQUOTE(JSON_EXTRACT(`eagr`.`name`, '$.fr')), ', ',
                `h`.`name`
            ))
            FROM `event_accommodation_room` `ear`
            LEFT JOIN `event_accommodation_room_groups` `eagr` ON `ear`.`room_group_id` = `eagr`.`id`
            LEFT JOIN `dictionnary_entries` `de` ON `ear`.`room_id` = `de`.`id`
            LEFT JOIN `event_accommodation` `ea` ON `eagr`.`event_accommodation_id` = `ea`.`id`
            LEFT JOIN `hotels` `h` ON `ea`.`hotel_id` = `h`.`id`
            WHERE `pd`.`shoppable_id` = `ear`.`id`
        )
    END AS `shoppable`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `country`,
    JSON_UNQUOTE(JSON_EXTRACT(`de_domain`.`name`, '$.fr')) AS `domain`,
    JSON_UNQUOTE(JSON_EXTRACT(`eg`.`title`, '$.fr')) AS `grant`
FROM `pec_distribution` `pd`
JOIN `orders` `o` ON `pd`.`order_id` = `o`.`id`
JOIN `events_contacts` `ec` ON `pd`.`event_contact_id` = `ec`.`id`
JOIN `users` `u` ON `ec`.`user_id` = `u`.`id`
LEFT JOIN (
    SELECT DISTINCT id, title FROM `event_sellable_service`
) `ess` ON `pd`.`shoppable_id` = `ess`.`id` AND `pd`.`type` = 'service'
LEFT JOIN (
    SELECT DISTINCT user_id, country_code FROM `account_address`
) `aa` ON `u`.`id` = `aa`.`user_id`
LEFT JOIN `countries` `c` ON `aa`.`country_code` = `c`.`code`
LEFT JOIN (
    SELECT DISTINCT user_id, domain_id FROM `account_profile`
) `ap` ON `u`.`id` = `ap`.`user_id`
LEFT JOIN `dictionnary_entries` `de_domain` ON `ap`.`domain_id` = `de_domain`.`id`
LEFT JOIN `event_grant` `eg` ON `pd`.`grant_id` = `eg`.`id`
GROUP BY
    `pd`.`grant_id`,
    `pd`.`order_id`,
    `o`.`event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`),
    `pd`.`type`,
    `ess`.`title`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')),
    JSON_UNQUOTE(JSON_EXTRACT(`de_domain`.`name`, '$.fr')),
    JSON_UNQUOTE(JSON_EXTRACT(`eg`.`title`, '$.fr'))
HAVING SUM(`pd`.`total_net`) <> 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW pec_order_view AS
SELECT
    `pd`.`type` AS `pec_distribution_type`,
    `pd`.`grant_id` AS `grant_id`,
    `pd`.`order_id` AS `order_id`,
    `o`.`event_id` AS `event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `participant`,
    FORMAT(SUM(DISTINCT `pd`.`total_net`) / 100.0, 2) AS `total_net`,
    FORMAT(SUM(DISTINCT `pd`.`total_vat`) / 100.0, 2) AS `total_vat`,
    FORMAT(SUM(DISTINCT (`pd`.`total_net` + `pd`.`total_vat`)) / 100.0, 2) AS `total`,
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
        WHEN `pd`.`type` IN ('accommodation', 'taxroom') THEN (
            SELECT GROUP_CONCAT(DISTINCT CONCAT(
                JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')), ', ',
                JSON_UNQUOTE(JSON_EXTRACT(`eagr`.`name`, '$.fr')), ', ',
                `h`.`name`
            ))
            FROM `event_accommodation_room` `ear`
            LEFT JOIN `event_accommodation_room_groups` `eagr` ON `ear`.`room_group_id` = `eagr`.`id`
            LEFT JOIN `dictionnary_entries` `de` ON `ear`.`room_id` = `de`.`id`
            LEFT JOIN `event_accommodation` `ea` ON `eagr`.`event_accommodation_id` = `ea`.`id`
            LEFT JOIN `hotels` `h` ON `ea`.`hotel_id` = `h`.`id`
            WHERE `pd`.`shoppable_id` = `ear`.`id`
        )
    END AS `shoppable`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `country`,
    JSON_UNQUOTE(JSON_EXTRACT(`de_domain`.`name`, '$.fr')) AS `domain`,
    JSON_UNQUOTE(JSON_EXTRACT(`eg`.`title`, '$.fr')) AS `grant`
FROM `pec_distribution` `pd`
JOIN `orders` `o` ON `pd`.`order_id` = `o`.`id`
JOIN `events_contacts` `ec` ON `pd`.`event_contact_id` = `ec`.`id`
JOIN `users` `u` ON `ec`.`user_id` = `u`.`id`
LEFT JOIN (
    SELECT DISTINCT id, title FROM `event_sellable_service`
) `ess` ON `pd`.`shoppable_id` = `ess`.`id` AND `pd`.`type` = 'service'
LEFT JOIN (
    SELECT DISTINCT user_id, country_code FROM `account_address`
) `aa` ON `u`.`id` = `aa`.`user_id`
LEFT JOIN `countries` `c` ON `aa`.`country_code` = `c`.`code`
LEFT JOIN (
    SELECT DISTINCT user_id, domain_id FROM `account_profile`
) `ap` ON `u`.`id` = `ap`.`user_id`
LEFT JOIN `dictionnary_entries` `de_domain` ON `ap`.`domain_id` = `de_domain`.`id`
LEFT JOIN `event_grant` `eg` ON `pd`.`grant_id` = `eg`.`id`
GROUP BY
    `pd`.`grant_id`,
    `pd`.`order_id`,
    `o`.`event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`),
    `pd`.`type`,
    `ess`.`title`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')),
    JSON_UNQUOTE(JSON_EXTRACT(`de_domain`.`name`, '$.fr')),
    JSON_UNQUOTE(JSON_EXTRACT(`eg`.`title`, '$.fr'))
HAVING SUM(`pd`.`total_net`) <> 0

        ");
    }
};
