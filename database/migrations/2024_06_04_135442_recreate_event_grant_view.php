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
        DB::statement("CREATE OR REPLACE VIEW event_grant_view AS
        SELECT
    `eg`.`id` AS `id`,
    `eg`.`deleted_at` AS `deleted_at`,
    `eg`.`event_id` AS `event_id`,
    json_unquote(json_extract(`eg`.`title`, '$.fr')) AS `title`,
    COALESCE(CONCAT(`egc`.`first_name`, ' ', `egc`.`last_name`), 'N/A') AS `contact`,
    json_unquote(json_extract(`eg`.`comment`, '$.fr')) AS `comment`,
    UPPER(`eg`.`amount_type`) AS `amount_type`,
    `eg`.`amount`,
    FORMAT(`eg`.`amount` / 100.0, 2) AS `amount_display`,
    `eg`.`pec_fee` AS `pec_fee`,
    FORMAT(`eg`.`pec_fee` / 100.0, 2) AS `pec_fee_display`,
    `eg`.`pax_avg` AS `pax_avg`,
    `eg`.`pax_max` AS `pax_max`,
    IF(`eg`.`active` = 1, 'Oui', 'Non') AS `active`,
    FORMAT(COALESCE(SUM(
        CASE
            WHEN `eg`.`amount_type` = 'ht' THEN `fr`.`amount_ht`
            ELSE `fr`.`amount_ttc`
        END
    ), 0) / 100.0, 2) AS `amount_used`,
    FORMAT((
        `eg`.`amount`
        - COALESCE(SUM(
            CASE
                WHEN `eg`.`amount_type` = 'ht' THEN `fr`.`amount_ht`
                ELSE `fr`.`amount_ttc`
            END
        ), 0)
    ) / 100.0, 2) AS `amount_remaining`,
    COALESCE(COUNT(DISTINCT `fr`.`order_id`), 0) AS `order_count`,
    COALESCE(COUNT(DISTINCT `o`.`client_id`), 0) AS `pax_count`
FROM
    `event_grant` `eg`
    LEFT JOIN `event_grant_contact` `egc` ON (`eg`.`id` = `egc`.`grant_id`)
    LEFT JOIN `event_grant_funding_records` `fr` ON (`eg`.`id` = `fr`.`grant_id`)
    LEFT JOIN `orders` `o` ON (`fr`.`order_id` = `o`.`id`)
    LEFT JOIN `events` `e` ON (`eg`.`event_id` = `e`.`id`)
WHERE
    `eg`.`deleted_at` IS NULL AND `e`.`deleted_at` IS NULL
GROUP BY
    `eg`.`id`,
    `eg`.`deleted_at`,
    `eg`.`event_id`,
    json_unquote(json_extract(`eg`.`title`, '$.fr')),
    COALESCE(CONCAT(`egc`.`first_name`, ' ', `egc`.`last_name`), 'N/A'),
    json_unquote(json_extract(`eg`.`comment`, '$.fr')),
    `eg`.`amount_type`,
    `eg`.`amount`,
    FORMAT(`eg`.`amount` / 100.0, 2),
    `eg`.`pec_fee`,
    FORMAT(`eg`.`pec_fee` / 100.0, 2),
    `eg`.`pax_avg`,
    `eg`.`pax_max`,
    `eg`.`active`
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // recreate as needed
    }
};
