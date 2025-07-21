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
        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_view AS

SELECT
    `s`.`id` AS `id`,
    `s`.`deleted_at` AS `deleted_at`,
    `s`.`event_id` AS `event_id`,
    json_unquote(json_extract(`s`.`title`, '$.fr')) AS `title`,
    date_format(`s`.`service_date`, '%d/%m/%Y') AS `service_date`,
    `s`.`published` AS `published`,
    `s`.`pec_eligible` AS `pec_eligible`,
    group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices`,
    `sb`.`stock` AS `stock`,
    `sb`.`stock_label` AS `stock_label`,
    `sb`.`bookings_count` AS `bookings_count`,
    `sb`.`temp_bookings_count` AS `temp_bookings_count`,
    `sb`.`temp_front_bookings_count` AS `temp_front_bookings_count`,
    `sb`.`total_bookings_count` AS `total_bookings_count`,
    `sb`.`available` AS `available`,
    `sb`.`available_label` AS `available_label`,
    -- Subquery for pec_paid_net
    FORMAT(IFNULL((
        SELECT SUM(ocs_paid.total_pec / (1 + (v_paid.rate/10000)) / 100)
        FROM `order_cart_service` ocs_paid
        JOIN `orders` o_paid ON o_paid.id = ocs_paid.order_id
        JOIN `vat` v_paid ON v_paid.id = ocs_paid.vat_id
        WHERE ocs_paid.service_id = s.id
        AND ocs_paid.total_pec > 0
        AND o_paid.status = 'paid'
        AND (ocs_paid.cancelled_at IS NULL AND o_paid.cancelled_at IS NULL)
    ), 0), 2) AS `pec_paid_net`,

    -- Subquery for pec_unpaid_net
    FORMAT(IFNULL((
        SELECT SUM(ocs_unpaid.total_pec / (1 + (v_unpaid.rate/10000)) / 100)
        FROM `order_cart_service` ocs_unpaid
        JOIN `orders` o_unpaid ON o_unpaid.id = ocs_unpaid.order_id
        JOIN `vat` v_unpaid ON v_unpaid.id = ocs_unpaid.vat_id
        WHERE ocs_unpaid.service_id = s.id
        AND ocs_unpaid.total_pec > 0
        AND o_unpaid.status = 'unpaid'
        AND (ocs_unpaid.cancelled_at IS NULL AND o_unpaid.cancelled_at IS NULL)
    ), 0), 2) AS `pec_unpaid_net`,

    -- Subquery for paid_net
    FORMAT(IFNULL((
        SELECT SUM(ocs_paid_net.total_net / 100)
        FROM `order_cart_service` ocs_paid_net
        JOIN `orders` o_paid_net ON o_paid_net.id = ocs_paid_net.order_id
        WHERE ocs_paid_net.service_id = s.id
        AND ocs_paid_net.total_net != 0
        AND o_paid_net.status = 'paid'
        AND (ocs_paid_net.cancelled_at IS NULL AND o_paid_net.cancelled_at IS NULL)
    ), 0), 2) AS `paid_net`,

    -- Subquery for unpaid_net
    FORMAT(IFNULL((
        SELECT SUM(ocs_unpaid_net.total_net / 100)
        FROM `order_cart_service` ocs_unpaid_net
        JOIN `orders` o_unpaid_net ON o_unpaid_net.id = ocs_unpaid_net.order_id
        WHERE ocs_unpaid_net.service_id = s.id
        AND ocs_unpaid_net.total_net != 0
        AND o_unpaid_net.status = 'unpaid'
        AND (ocs_unpaid_net.cancelled_at IS NULL AND o_unpaid_net.cancelled_at IS NULL)
    ), 0), 2) AS `unpaid_net`
FROM
    (`event_sellable_service` `s`
    LEFT JOIN `event_sellable_service_prices` `esp` ON (`esp`.`event_sellable_service_id` = `s`.`id`))
    LEFT JOIN `event_sellable_service_stock_view` `sb` ON (`sb`.`id` = `s`.`id`)
GROUP BY
    `s`.`id`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_view AS
        select `s`.`id`                                                                                                  AS `id`,
       `s`.`deleted_at`                                                                                          AS `deleted_at`,
       `s`.`event_id`                                                                                            AS `event_id`,
       json_unquote(json_extract(`s`.`title`, '$.fr'))                                                           AS `title`,
       date_format(`s`.`service_date`, '%d/%m/%Y')                                                               AS `service_date`,
       `s`.`published`                                                                                           AS `published`,
       `s`.`pec_eligible`                                                                                        AS `pec_eligible`,
       group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator
                    ',<br>')                                                                                     AS `prices`,
       `sb`.`stock`                                                                                              AS `stock`,
       `sb`.`stock_label`                                                                                        AS `stock_label`,
       `sb`.`bookings_count`                                                                                     AS `bookings_count`,
       `sb`.`temp_bookings_count`                                                                                AS `temp_bookings_count`,
       `sb`.`temp_front_bookings_count`                                                                          AS `temp_front_bookings_count`,
       `sb`.`total_bookings_count`                                                                               AS `total_bookings_count`,
       `sb`.`available`                                                                                          AS `available`,
       `sb`.`available_label`                                                                                    AS `available_label`
from ((`event_sellable_service` `s` left join `event_sellable_service_prices` `esp`
       on (`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb`
      on (`sb`.`id` = `s`.`id`))
group by `s`.`id`");
    }
};
