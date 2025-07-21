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
       `sb`.`available_label`                                                                                    AS `available_label`,
       format(ifnull((select sum(`ocs_paid`.`total_pec` / (1 + `v_paid`.`rate` / 10000) / 100)
                      from ((`order_cart_service` `ocs_paid` join `orders` `o_paid`
                             on (`o_paid`.`id` = `ocs_paid`.`order_id`)) join `vat` `v_paid`
                            on (`v_paid`.`id` = `ocs_paid`.`vat_id`))
                      where `ocs_paid`.`service_id` = `s`.`id`
                        and `ocs_paid`.`total_pec` > 0
                        and `o_paid`.`status` = 'paid'
                        and `ocs_paid`.`cancelled_at` is null
                        and `o_paid`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `pec_paid_net`,
       format(ifnull((select sum(`ocs_unpaid`.`total_pec` / (1 + `v_unpaid`.`rate` / 10000) / 100)
                      from ((`order_cart_service` `ocs_unpaid` join `orders` `o_unpaid`
                             on (`o_unpaid`.`id` = `ocs_unpaid`.`order_id`)) join `vat` `v_unpaid`
                            on (`v_unpaid`.`id` = `ocs_unpaid`.`vat_id`))
                      where `ocs_unpaid`.`service_id` = `s`.`id`
                        and `ocs_unpaid`.`total_pec` > 0
                        and `o_unpaid`.`status` = 'unpaid'
                        and `ocs_unpaid`.`cancelled_at` is null
                        and `o_unpaid`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `pec_unpaid_net`,
       format(ifnull((select sum(`ocs_paid_net`.`total_net` / 100)
                      from (`order_cart_service` `ocs_paid_net` join `orders` `o_paid_net`
                            on (`o_paid_net`.`id` = `ocs_paid_net`.`order_id`))
                      where `ocs_paid_net`.`service_id` = `s`.`id`
                        and `ocs_paid_net`.`total_net` <> 0
                        and `o_paid_net`.`status` = 'paid'
                        and `ocs_paid_net`.`cancelled_at` is null
                        and `o_paid_net`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `paid_net`,
       format(ifnull((select sum(`ocs_unpaid_net`.`total_net` / 100)
                      from (`order_cart_service` `ocs_unpaid_net` join `orders` `o_unpaid_net`
                            on (`o_unpaid_net`.`id` = `ocs_unpaid_net`.`order_id`))
                      where `ocs_unpaid_net`.`service_id` = `s`.`id`
                        and `ocs_unpaid_net`.`total_net` <> 0
                        and `o_unpaid_net`.`status` = 'unpaid'
                        and `ocs_unpaid_net`.`cancelled_at` is null
                        and `o_unpaid_net`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `unpaid_net`,
       -- Congress Net Amount
       format(ifnull((select sum(`ocs_congress`.`total_net` / 100)
                      from ((`order_cart_service` `ocs_congress`
                            join `orders` `o_congress` on (`o_congress`.`id` = `ocs_congress`.`order_id`))
                            join `events_contacts` `ec_congress` on (`o_congress`.`client_id` = `ec_congress`.`id`))
                            join `participation_types` `pt_congress` on (`ec_congress`.`participation_type_id` = `pt_congress`.`id`)
                      where `ocs_congress`.`service_id` = `s`.`id`
                        and `ocs_congress`.`total_net` <> 0
                        and `o_congress`.`client_type` != 'group'
                        and `pt_congress`.`group` = 'congress'
                        and `ocs_congress`.`cancelled_at` is null
                        and `o_congress`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `congress_net`,
       -- Industry Net Amount
       format(ifnull((select sum(`ocs_industry`.`total_net` / 100)
                      from ((`order_cart_service` `ocs_industry`
                            join `orders` `o_industry` on (`o_industry`.`id` = `ocs_industry`.`order_id`))
                            join `events_contacts` `ec_industry` on (`o_industry`.`client_id` = `ec_industry`.`id`))
                            join `participation_types` `pt_industry` on (`ec_industry`.`participation_type_id` = `pt_industry`.`id`)
                      where `ocs_industry`.`service_id` = `s`.`id`
                        and `ocs_industry`.`total_net` <> 0
                        and `o_industry`.`client_type` != 'group'
                        and `pt_industry`.`group` = 'industry'
                        and `ocs_industry`.`cancelled_at` is null
                        and `o_industry`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `industry_net`,
       -- Orators Net Amount
       format(ifnull((select sum(`ocs_orator`.`total_net` / 100)
                      from ((`order_cart_service` `ocs_orator`
                            join `orders` `o_orator` on (`o_orator`.`id` = `ocs_orator`.`order_id`))
                            join `events_contacts` `ec_orator` on (`o_orator`.`client_id` = `ec_orator`.`id`))
                            join `participation_types` `pt_orator` on (`ec_orator`.`participation_type_id` = `pt_orator`.`id`)
                      where `ocs_orator`.`service_id` = `s`.`id`
                        and `ocs_orator`.`total_net` <> 0
                        and `o_orator`.`client_type` != 'group'
                        and `pt_orator`.`group` = 'orator'
                        and `ocs_orator`.`cancelled_at` is null
                        and `o_orator`.`cancelled_at` is null), 0),
              2)                                                                                                 AS `orators_net`
from ((`event_sellable_service` `s` left join `event_sellable_service_prices` `esp`
       on (`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb`
      on (`sb`.`id` = `s`.`id`))
group by `s`.`id`
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_view as
        select `s`.`id` AS `id`,`s`.`deleted_at` AS `deleted_at`,`s`.`event_id` AS `event_id`,json_unquote(json_extract(`s`.`title`,'$.fr')) AS `title`,date_format(`s`.`service_date`,'%d/%m/%Y') AS `service_date`,`s`.`published` AS `published`,`s`.`pec_eligible` AS `pec_eligible`,group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices`,`sb`.`stock` AS `stock`,`sb`.`stock_label` AS `stock_label`,`sb`.`bookings_count` AS `bookings_count`,`sb`.`temp_bookings_count` AS `temp_bookings_count`,`sb`.`temp_front_bookings_count` AS `temp_front_bookings_count`,`sb`.`total_bookings_count` AS `total_bookings_count`,`sb`.`available` AS `available`,`sb`.`available_label` AS `available_label`,format(ifnull((select sum(`ocs_paid`.`total_pec` / (1 + `v_paid`.`rate` / 10000) / 100) from ((`order_cart_service` `ocs_paid` join `orders` `o_paid` on(`o_paid`.`id` = `ocs_paid`.`order_id`)) join `vat` `v_paid` on(`v_paid`.`id` = `ocs_paid`.`vat_id`)) where `ocs_paid`.`service_id` = `s`.`id` and `ocs_paid`.`total_pec` > 0 and `o_paid`.`status` = 'paid' and `ocs_paid`.`cancelled_at` is null and `o_paid`.`cancelled_at` is null),0),2) AS `pec_paid_net`,format(ifnull((select sum(`ocs_unpaid`.`total_pec` / (1 + `v_unpaid`.`rate` / 10000) / 100) from ((`order_cart_service` `ocs_unpaid` join `orders` `o_unpaid` on(`o_unpaid`.`id` = `ocs_unpaid`.`order_id`)) join `vat` `v_unpaid` on(`v_unpaid`.`id` = `ocs_unpaid`.`vat_id`)) where `ocs_unpaid`.`service_id` = `s`.`id` and `ocs_unpaid`.`total_pec` > 0 and `o_unpaid`.`status` = 'unpaid' and `ocs_unpaid`.`cancelled_at` is null and `o_unpaid`.`cancelled_at` is null),0),2) AS `pec_unpaid_net`,format(ifnull((select sum(`ocs_paid_net`.`total_net` / 100) from (`order_cart_service` `ocs_paid_net` join `orders` `o_paid_net` on(`o_paid_net`.`id` = `ocs_paid_net`.`order_id`)) where `ocs_paid_net`.`service_id` = `s`.`id` and `ocs_paid_net`.`total_net` <> 0 and `o_paid_net`.`status` = 'paid' and `ocs_paid_net`.`cancelled_at` is null and `o_paid_net`.`cancelled_at` is null),0),2) AS `paid_net`,format(ifnull((select sum(`ocs_unpaid_net`.`total_net` / 100) from (`order_cart_service` `ocs_unpaid_net` join `orders` `o_unpaid_net` on(`o_unpaid_net`.`id` = `ocs_unpaid_net`.`order_id`)) where `ocs_unpaid_net`.`service_id` = `s`.`id` and `ocs_unpaid_net`.`total_net` <> 0 and `o_unpaid_net`.`status` = 'unpaid' and `ocs_unpaid_net`.`cancelled_at` is null and `o_unpaid_net`.`cancelled_at` is null),0),2) AS `unpaid_net` from ((`event_sellable_service` `s` left join `event_sellable_service_prices` `esp` on(`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb` on(`sb`.`id` = `s`.`id`)) group by `s`.`id`");
    }
};
