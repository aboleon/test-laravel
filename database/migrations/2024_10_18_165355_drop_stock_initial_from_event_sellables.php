<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            $table->dropColumn('stock_initial');
        });
        /**
         * Create the stock view
         */
        DB::statement(
            "CREATE OR REPLACE VIEW event_sellable_service_stock_view AS
SELECT
    CAST(`s`.`id` AS INT) AS `id`,
    CAST(`s`.`event_id` AS INT) AS `event_id`,

    -- Stock value, casting the 'Illimité' equivalent as 99999
    CAST(CASE
        WHEN `s`.`stock_unlimited` = 1 THEN 99999
        ELSE `s`.`stock`
    END AS INT) AS `stock`,

    -- Stock label as text
    CASE
        WHEN `s`.`stock_unlimited` = 1 THEN 'Illimité'
        ELSE CAST(`s`.`stock` AS CHAR)
    END AS `stock_label`,

    -- Adjusted bookings count from order_cart_service
    CAST(COALESCE(
        (SELECT SUM(ocs_inner.quantity)
         FROM `order_cart_service` ocs_inner
         WHERE ocs_inner.service_id = s.id), 0) AS INT) AS `bookings_count`,

    -- Temp bookings count from order_temp_stock (using a subquery to avoid row duplication)
    CAST(COALESCE(
        (SELECT SUM(`ots`.`quantity`)
         FROM `order_temp_stock` `ots`
         WHERE `ots`.`shoppable_id` = `s`.`id`
           AND `ots`.`shoppable_type` LIKE '%Sellable'), 0) AS INT) AS `temp_bookings_count`,

    -- Temp front bookings count from front_cart_lines (when order_id is null, using a subquery)
    CAST(COALESCE(
        (SELECT SUM(`fcl`.`quantity`)
         FROM `front_cart_lines` `fcl`
         LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id`
         WHERE `fcl`.`shoppable_id` = `s`.`id`
           AND `fcl`.`shoppable_type` LIKE '%Sellable'
           AND `fc`.`order_id` IS NULL), 0) AS INT) AS `temp_front_bookings_count`,

    -- Total bookings count (sum of the three counts using subqueries)
    CAST(
        (COALESCE(
            (SELECT SUM(ocs_inner.quantity)
             FROM `order_cart_service` ocs_inner
             WHERE ocs_inner.service_id = s.id), 0)
        + COALESCE(
            (SELECT SUM(`ots`.`quantity`)
             FROM `order_temp_stock` `ots`
             WHERE `ots`.`shoppable_id` = `s`.`id`
               AND `ots`.`shoppable_type` LIKE '%Sellable'), 0)
        + COALESCE(
            (SELECT SUM(`fcl`.`quantity`)
             FROM `front_cart_lines` `fcl`
             LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id`
             WHERE `fcl`.`shoppable_id` = `s`.`id`
               AND `fcl`.`shoppable_type` LIKE '%Sellable'
               AND `fc`.`order_id` IS NULL), 0)) AS INT) AS `total_bookings_count`,

    -- Available stock label as text
    CASE
        WHEN `s`.`stock_unlimited` = 1 THEN 'Illimité'
        ELSE CAST((`s`.`stock`
            - COALESCE(
                (SELECT SUM(ocs_inner.quantity)
                 FROM `order_cart_service` ocs_inner
                 WHERE ocs_inner.service_id = s.id), 0)
            - COALESCE(
                (SELECT SUM(`ots`.`quantity`)
                 FROM `order_temp_stock` `ots`
                 WHERE `ots`.`shoppable_id` = `s`.`id`
                   AND `ots`.`shoppable_type` LIKE '%Sellable'), 0)
            - COALESCE(
                (SELECT SUM(`fcl`.`quantity`)
                 FROM `front_cart_lines` `fcl`
                 LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id`
                 WHERE `fcl`.`shoppable_id` = `s`.`id`
                   AND `fcl`.`shoppable_type` LIKE '%Sellable'
                   AND `fc`.`order_id` IS NULL), 0)) AS CHAR)
    END AS `available_label`,

    -- Available stock calculation, casting as INT
    CAST(CASE
        WHEN `s`.`stock_unlimited` = 1 THEN 99999
        ELSE (`s`.`stock`
            - COALESCE(
                (SELECT SUM(ocs_inner.quantity)
                 FROM `order_cart_service` ocs_inner
                 WHERE ocs_inner.service_id = s.id), 0)
            - COALESCE(
                (SELECT SUM(`ots`.`quantity`)
                 FROM `order_temp_stock` `ots`
                 WHERE `ots`.`shoppable_id` = `s`.`id`
                   AND `ots`.`shoppable_type` LIKE '%Sellable'), 0)
            - COALESCE(
                (SELECT SUM(`fcl`.`quantity`)
                 FROM `front_cart_lines` `fcl`
                 LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id`
                 WHERE `fcl`.`shoppable_id` = `s`.`id`
                   AND `fcl`.`shoppable_type` LIKE '%Sellable'
                   AND `fc`.`order_id` IS NULL), 0))
    END AS INT) AS `available`

FROM `event_sellable_service` `s`

GROUP BY `s`.`id`;

        ",
        );


        /**
         * Create the stock view
         */
        DB::statement(
            "CREATE OR REPLACE VIEW event_sellable_service_view AS
SELECT
    `s`.`id` AS `id`,
    `s`.`event_id` AS `event_id`,
    json_unquote(json_extract(`s`.`title`, '$.fr')) AS `title`,
    `s`.`is_invitation` AS `is_invitation`,
    case when `s`.`is_invitation` = 1 then 'Oui' else 'Non' end AS `is_invitation_display`,
    json_unquote(json_extract(`sg`.`name`, '$.fr')) AS `group`,
    date_format(`s`.`service_date`, '%d/%m/%Y') AS `service_date`,
    `s`.`published` AS `published`,
    `s`.`pec_eligible` AS `pec_eligible`,
    group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices`,

    -- Fetch the missing columns from the event_sellable_service_stock_view
    `sb`.`stock`,
    `sb`.`stock_label`,
    `sb`.`bookings_count`,
    `sb`.`temp_bookings_count`,
    `sb`.`temp_front_bookings_count`,
    `sb`.`total_bookings_count`,
    `sb`.`available`,
    `sb`.`available_label`

FROM `event_sellable_service` `s`
LEFT JOIN `dictionnary_entries` `sg` ON (`sg`.`id` = `s`.`service_group`)
LEFT JOIN `event_sellable_service_prices` `esp` ON (`esp`.`event_sellable_service_id` = `s`.`id`)
LEFT JOIN `event_sellable_service_stock_view` `sb` ON `sb`.`id` = `s`.`id`
GROUP BY `s`.`id`;

            ",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            $table->unsignedInteger('stock_initial')->after('stock');
        });
        DB::statement(
            "CREATE OR REPLACE VIEW event_sellable_service_view AS
        select `s`.`id` AS `id`,`s`.`event_id` AS `event_id`,json_unquote(json_extract(`s`.`title`,'$.fr')) AS `title_fr`,`s`.`is_invitation` AS `is_invitation`,case when `s`.`is_invitation` = 1 then 'Oui' else 'Non' end AS `is_invitation_display`,json_unquote(json_extract(`sg`.`name`,'$.fr')) AS `group_fr`,date_format(`s`.`service_date`,'%d/%m/%Y') AS `service_date_fr`,`s`.`stock_initial` AS `stock_initial`,cast(`s`.`stock_initial` as signed) - cast(`s`.`stock` as signed) AS `reserved`,`s`.`stock` AS `stock`,`s`.`published` AS `published`,`s`.`pec_eligible` AS `pec_eligible`,group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices` from ((`event_sellable_service` `s` left join `dictionnary_entries` `sg` on(`sg`.`id` = `s`.`service_group`)) left join `event_sellable_service_prices` `esp` on(`esp`.`event_sellable_service_id` = `s`.`id`)) group by `s`.`id`
        ",
        );
    }
};
