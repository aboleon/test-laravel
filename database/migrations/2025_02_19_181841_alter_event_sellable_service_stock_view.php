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
        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_stock_view AS
        SELECT
    CAST(`s`.`id` AS SIGNED) AS `id`,
    CAST(`s`.`event_id` AS SIGNED) AS `event_id`,
    CAST(CASE WHEN `s`.`stock_unlimited` = 1 THEN 99999 ELSE `s`.`stock` END AS SIGNED) AS `stock`,
    CASE
        WHEN `s`.`stock_unlimited` = 1 THEN 'Illimité'
        ELSE CAST(`s`.`stock` AS CHAR CHARSET utf8mb4)
    END AS `stock_label`,
    CAST(COALESCE((SELECT SUM(`ocs_inner`.`quantity`)
                   FROM `order_cart_service` `ocs_inner`
                   WHERE `ocs_inner`.`service_id` = `s`.`id`), 0) AS SIGNED) AS `bookings_count`,
    CAST(COALESCE((SELECT SUM(`ots`.`quantity`)
                   FROM `order_temp_stock` `ots`
                   WHERE `ots`.`shoppable_id` = `s`.`id`
                     AND `ots`.`shoppable_type` LIKE '%Sellable'), 0) AS SIGNED) AS `temp_bookings_count`,
    CAST(COALESCE((SELECT SUM(`fcl`.`quantity`)
                   FROM (`front_cart_lines` `fcl` LEFT JOIN `front_carts` `fc`
                         ON (`fc`.`id` = `fcl`.`front_cart_id`))
                   WHERE `fcl`.`shoppable_id` = `s`.`id`
                     AND `fcl`.`shoppable_type` LIKE '%Sellable'
                     AND `fc`.`order_id` IS NULL), 0) AS SIGNED) AS `temp_front_bookings_count`,
    -- Add cancelled_bookings_count
    CAST(COALESCE((SELECT SUM(`ocs_cancelled`.`quantity`)
                   FROM `order_cart_service` `ocs_cancelled`
                   WHERE `ocs_cancelled`.`service_id` = `s`.`id`
                     AND `ocs_cancelled`.`cancelled_at` IS NOT NULL), 0) AS SIGNED) AS `cancelled_bookings_count`,
    -- Adjusted total_bookings_count (subtracts cancelled)
    CAST(
        COALESCE((SELECT SUM(`ocs_inner`.`quantity`) FROM `order_cart_service` `ocs_inner` WHERE `ocs_inner`.`service_id` = `s`.`id`), 0) +
        COALESCE((SELECT SUM(`ots`.`quantity`) FROM `order_temp_stock` `ots` WHERE `ots`.`shoppable_id` = `s`.`id` AND `ots`.`shoppable_type` LIKE '%Sellable'), 0) +
        COALESCE((SELECT SUM(`fcl`.`quantity`) FROM `front_cart_lines` `fcl` LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id` WHERE `fcl`.`shoppable_id` = `s`.`id` AND `fcl`.`shoppable_type` LIKE '%Sellable' AND `fc`.`order_id` IS NULL), 0) -
        COALESCE((SELECT SUM(`ocs_cancelled`.`quantity`) FROM `order_cart_service` `ocs_cancelled` WHERE `ocs_cancelled`.`service_id` = `s`.`id` AND `ocs_cancelled`.`cancelled_at` IS NOT NULL), 0)
    AS SIGNED) AS `total_bookings_count`,
    -- Corrected available_label (adds back cancelled bookings)
    CASE
        WHEN `s`.`stock_unlimited` = 1 THEN 'Illimité'
        ELSE CAST(`s`.`stock`
                 - COALESCE((SELECT SUM(`ocs_inner`.`quantity`) FROM `order_cart_service` `ocs_inner` WHERE `ocs_inner`.`service_id` = `s`.`id`), 0)
                 - COALESCE((SELECT SUM(`ots`.`quantity`) FROM `order_temp_stock` `ots` WHERE `ots`.`shoppable_id` = `s`.`id` AND `ots`.`shoppable_type` LIKE '%Sellable'), 0)
                 - COALESCE((SELECT SUM(`fcl`.`quantity`) FROM `front_cart_lines` `fcl` LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id` WHERE `fcl`.`shoppable_id` = `s`.`id` AND `fcl`.`shoppable_type` LIKE '%Sellable' AND `fc`.`order_id` IS NULL), 0)
                 + COALESCE((SELECT SUM(`ocs_cancelled`.`quantity`) FROM `order_cart_service` `ocs_cancelled` WHERE `ocs_cancelled`.`service_id` = `s`.`id` AND `ocs_cancelled`.`cancelled_at` IS NOT NULL), 0)
                 AS CHAR CHARSET utf8mb4)
    END AS `available_label`,
    -- Corrected available (adds back cancelled bookings)
    CAST(CASE
             WHEN `s`.`stock_unlimited` = 1 THEN 99999
             ELSE `s`.`stock`
                  - COALESCE((SELECT SUM(`ocs_inner`.`quantity`) FROM `order_cart_service` `ocs_inner` WHERE `ocs_inner`.`service_id` = `s`.`id`), 0)
                  - COALESCE((SELECT SUM(`ots`.`quantity`) FROM `order_temp_stock` `ots` WHERE `ots`.`shoppable_id` = `s`.`id` AND `ots`.`shoppable_type` LIKE '%Sellable'), 0)
                  - COALESCE((SELECT SUM(`fcl`.`quantity`) FROM `front_cart_lines` `fcl` LEFT JOIN `front_carts` `fc` ON `fc`.`id` = `fcl`.`front_cart_id` WHERE `fcl`.`shoppable_id` = `s`.`id` AND `fcl`.`shoppable_type` LIKE '%Sellable' AND `fc`.`order_id` IS NULL), 0)
                  + COALESCE((SELECT SUM(`ocs_cancelled`.`quantity`) FROM `order_cart_service` `ocs_cancelled` WHERE `ocs_cancelled`.`service_id` = `s`.`id` AND `ocs_cancelled`.`cancelled_at` IS NOT NULL), 0)
    END AS SIGNED) AS `available`
FROM
    `event_sellable_service` `s`
GROUP BY
    `s`.`id`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_stock_view AS
        select cast(`s`.`id` as signed) AS `id`,cast(`s`.`event_id` as signed) AS `event_id`,cast(case when `s`.`stock_unlimited` = 1 then 99999 else `s`.`stock` end as signed) AS `stock`,case when `s`.`stock_unlimited` = 1 then 'Illimité' else cast(`s`.`stock` as char charset utf8mb4) end AS `stock_label`,cast(coalesce((select sum(`ocs_inner`.`quantity`) from `order_cart_service` `ocs_inner` where `ocs_inner`.`service_id` = `s`.`id`),0) as signed) AS `bookings_count`,cast(coalesce((select sum(`ots`.`quantity`) from `order_temp_stock` `ots` where `ots`.`shoppable_id` = `s`.`id` and `ots`.`shoppable_type` like '%Sellable'),0) as signed) AS `temp_bookings_count`,cast(coalesce((select sum(`fcl`.`quantity`) from (`front_cart_lines` `fcl` left join `front_carts` `fc` on(`fc`.`id` = `fcl`.`front_cart_id`)) where `fcl`.`shoppable_id` = `s`.`id` and `fcl`.`shoppable_type` like '%Sellable' and `fc`.`order_id` is null),0) as signed) AS `temp_front_bookings_count`,cast(coalesce((select sum(`ocs_inner`.`quantity`) from `order_cart_service` `ocs_inner` where `ocs_inner`.`service_id` = `s`.`id`),0) + coalesce((select sum(`ots`.`quantity`) from `order_temp_stock` `ots` where `ots`.`shoppable_id` = `s`.`id` and `ots`.`shoppable_type` like '%Sellable'),0) + coalesce((select sum(`fcl`.`quantity`) from (`front_cart_lines` `fcl` left join `front_carts` `fc` on(`fc`.`id` = `fcl`.`front_cart_id`)) where `fcl`.`shoppable_id` = `s`.`id` and `fcl`.`shoppable_type` like '%Sellable' and `fc`.`order_id` is null),0) as signed) AS `total_bookings_count`,case when `s`.`stock_unlimited` = 1 then 'Illimité' else cast(`s`.`stock` - coalesce((select sum(`ocs_inner`.`quantity`) from `order_cart_service` `ocs_inner` where `ocs_inner`.`service_id` = `s`.`id`),0) - coalesce((select sum(`ots`.`quantity`) from `order_temp_stock` `ots` where `ots`.`shoppable_id` = `s`.`id` and `ots`.`shoppable_type` like '%Sellable'),0) - coalesce((select sum(`fcl`.`quantity`) from (`front_cart_lines` `fcl` left join `front_carts` `fc` on(`fc`.`id` = `fcl`.`front_cart_id`)) where `fcl`.`shoppable_id` = `s`.`id` and `fcl`.`shoppable_type` like '%Sellable' and `fc`.`order_id` is null),0) as char charset utf8mb4) end AS `available_label`,cast(case when `s`.`stock_unlimited` = 1 then 99999 else `s`.`stock` - coalesce((select sum(`ocs_inner`.`quantity`) from `order_cart_service` `ocs_inner` where `ocs_inner`.`service_id` = `s`.`id`),0) - coalesce((select sum(`ots`.`quantity`) from `order_temp_stock` `ots` where `ots`.`shoppable_id` = `s`.`id` and `ots`.`shoppable_type` like '%Sellable'),0) - coalesce((select sum(`fcl`.`quantity`) from (`front_cart_lines` `fcl` left join `front_carts` `fc` on(`fc`.`id` = `fcl`.`front_cart_id`)) where `fcl`.`shoppable_id` = `s`.`id` and `fcl`.`shoppable_type` like '%Sellable' and `fc`.`order_id` is null),0) end as signed) AS `available` from `event_sellable_service` `s` group by `s`.`id`");
    }
};
