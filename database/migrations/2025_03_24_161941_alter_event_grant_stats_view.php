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
    DATE_FORMAT(`pd`.`created_at`,'%d/%m/%Y') AS `created_at`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `participant`,
    FORMAT(
        SUM(COALESCE(`pd`.`total_net`, 0)) / 100.0, 2
    ) AS `total_net`,
    FORMAT(
        SUM(COALESCE(`pd`.`total_vat`, 0)) / 100.0, 2
    ) AS `total_vat`,
    FORMAT(
        SUM(
            CASE
                WHEN `pd`.`type` = 'transport' THEN COALESCE(`pd`.`unit_price`, 0)
                ELSE COALESCE(`pd`.`total_net`, 0) + COALESCE(`pd`.`total_vat`, 0)
            END
        ) / 100.0, 2
    ) AS `total`,
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
        WHEN `pd`.`type` IN ('accommodation', 'taxroom') THEN
            GROUP_CONCAT(DISTINCT CONCAT(
                JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')), ', ',
                JSON_UNQUOTE(JSON_EXTRACT(`eagr`.`name`, '$.fr')), ', ',
                `h`.`name`
            ) SEPARATOR ',')
    END AS `shoppable`,
    `aa`.`locality` AS `locality`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `country`,
    `eg`.`amount_type` AS `grant_type`,
    JSON_UNQUOTE(JSON_EXTRACT(`eg`.`title`, '$.fr')) AS `grant_title`
FROM
    `pec_distribution` `pd`
LEFT JOIN `orders` `o` ON `pd`.`order_id` = `o`.`id`
LEFT JOIN `events_contacts` `ec` ON `pd`.`event_contact_id` = `ec`.`id`
LEFT JOIN `users` `u` ON `ec`.`user_id` = `u`.`id`
LEFT JOIN `event_grant` `eg` ON `pd`.`grant_id` = `eg`.`id`
LEFT JOIN `event_sellable_service` `ess`
    ON `pd`.`shoppable_id` = `ess`.`id` AND `pd`.`type` = 'service'
LEFT JOIN `event_accommodation_room` `ear`
    ON `pd`.`shoppable_id` = `ear`.`id` AND `pd`.`type` IN ('accommodation', 'taxroom')
LEFT JOIN `event_accommodation_room_groups` `eagr`
    ON `ear`.`room_group_id` = `eagr`.`id`
LEFT JOIN `dictionnary_entries` `de`
    ON `ear`.`room_id` = `de`.`id`
LEFT JOIN `event_accommodation` `ea`
    ON `eagr`.`event_accommodation_id` = `ea`.`id`
LEFT JOIN `hotels` `h`
    ON `ea`.`hotel_id` = `h`.`id`
LEFT JOIN `order_invoiceable` `oi`
    ON `oi`.`order_id` = `o`.`id`
LEFT JOIN `account_address` `aa`
    ON (
        (`pd`.`type` = 'transport' AND `aa`.`user_id` = `ec`.`user_id` AND (`aa`.`billing` = 1 OR `aa`.`billing` IS NULL))
        OR (`pd`.`type` != 'transport' AND `aa`.`id` = `oi`.`address_id`)
    )
LEFT JOIN `countries` `c`
    ON `c`.`code` = `aa`.`country_code`
GROUP BY
    `pd`.`grant_id`, `pd`.`order_id`, `o`.`event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`),
    `pd`.`type`, `ess`.`title`, `aa`.`locality`, `c`.`name`, `eg`.`amount_type`
HAVING
    SUM(
        CASE
            WHEN `pd`.`type` = 'transport' THEN COALESCE(`pd`.`unit_price`, 0)
            ELSE COALESCE(`pd`.`total_net`, 0) + COALESCE(`pd`.`total_vat`, 0)
        END
    ) <> 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_stats AS
SELECT
    `pd`.`grant_id` AS `grant_id`,
    `pd`.`order_id` AS `order_id`,
    `pd`.`type` AS `type_row`,
    `o`.`event_id` AS `event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `participant`,
    FORMAT(
        SUM(COALESCE(`pd`.`total_net`, 0)) / 100.0, 2
    ) AS `total_net`,
    FORMAT(
        SUM(COALESCE(`pd`.`total_vat`, 0)) / 100.0, 2
    ) AS `total_vat`,
    FORMAT(
        SUM(
            CASE
                WHEN `pd`.`type` = 'transport' THEN COALESCE(`pd`.`unit_price`, 0)
                ELSE COALESCE(`pd`.`total_net`, 0) + COALESCE(`pd`.`total_vat`, 0)
            END
        ) / 100.0, 2
    ) AS `total`,
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
        WHEN `pd`.`type` IN ('accommodation', 'taxroom') THEN
            GROUP_CONCAT(DISTINCT CONCAT(
                JSON_UNQUOTE(JSON_EXTRACT(`de`.`name`, '$.fr')), ', ',
                JSON_UNQUOTE(JSON_EXTRACT(`eagr`.`name`, '$.fr')), ', ',
                `h`.`name`
            ) SEPARATOR ',')
    END AS `shoppable`,
    `aa`.`locality` AS `locality`,
    JSON_UNQUOTE(JSON_EXTRACT(`c`.`name`, '$.fr')) AS `country`,
    `eg`.`amount_type` AS `grant_type`
FROM
    `pec_distribution` `pd`
LEFT JOIN `orders` `o` ON `pd`.`order_id` = `o`.`id`
LEFT JOIN `events_contacts` `ec` ON `pd`.`event_contact_id` = `ec`.`id`
LEFT JOIN `users` `u` ON `ec`.`user_id` = `u`.`id`
LEFT JOIN `event_grant` `eg` ON `pd`.`grant_id` = `eg`.`id`
LEFT JOIN `event_sellable_service` `ess`
    ON `pd`.`shoppable_id` = `ess`.`id` AND `pd`.`type` = 'service'
LEFT JOIN `event_accommodation_room` `ear`
    ON `pd`.`shoppable_id` = `ear`.`id` AND `pd`.`type` IN ('accommodation', 'taxroom')
LEFT JOIN `event_accommodation_room_groups` `eagr`
    ON `ear`.`room_group_id` = `eagr`.`id`
LEFT JOIN `dictionnary_entries` `de`
    ON `ear`.`room_id` = `de`.`id`
LEFT JOIN `event_accommodation` `ea`
    ON `eagr`.`event_accommodation_id` = `ea`.`id`
LEFT JOIN `hotels` `h`
    ON `ea`.`hotel_id` = `h`.`id`
LEFT JOIN `order_invoiceable` `oi`
    ON `oi`.`order_id` = `o`.`id`
LEFT JOIN `account_address` `aa`
    ON (
        (`pd`.`type` = 'transport' AND `aa`.`user_id` = `ec`.`user_id` AND (`aa`.`billing` = 1 OR `aa`.`billing` IS NULL))
        OR (`pd`.`type` != 'transport' AND `aa`.`id` = `oi`.`address_id`)
    )
LEFT JOIN `countries` `c`
    ON `c`.`code` = `aa`.`country_code`
GROUP BY
    `pd`.`grant_id`, `pd`.`order_id`, `o`.`event_id`,
    CONCAT(`u`.`first_name`, ' ', `u`.`last_name`),
    `pd`.`type`, `ess`.`title`, `aa`.`locality`, `c`.`name`, `eg`.`amount_type`
HAVING
    SUM(
        CASE
            WHEN `pd`.`type` = 'transport' THEN COALESCE(`pd`.`unit_price`, 0)
            ELSE COALESCE(`pd`.`total_net`, 0) + COALESCE(`pd`.`total_vat`, 0)
        END
    ) <> 0
        ");
    }
};
