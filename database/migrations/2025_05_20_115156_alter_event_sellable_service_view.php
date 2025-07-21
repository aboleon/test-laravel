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
        select
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

    -- PEC calculations for non-group orders
    format(ifnull((select sum(`ocs_paid`.`total_pec` / (1 + `v_paid`.`rate` / 10000) / 100)
                  from ((`order_cart_service` `ocs_paid` join `orders` `o_paid`
                         on (`o_paid`.`id` = `ocs_paid`.`order_id`)) join `vat` `v_paid`
                        on (`v_paid`.`id` = `ocs_paid`.`vat_id`))
                  where `ocs_paid`.`service_id` = `s`.`id`
                    and `ocs_paid`.`total_pec` > 0
                    and `o_paid`.`status` = 'paid'
                    and `o_paid`.`client_type` <> 'group'
                    and `ocs_paid`.`cancelled_at` is null
                    and `o_paid`.`cancelled_at` is null), 0),
          2) AS `pec_paid_net`,

    format(ifnull((select sum(`ocs_unpaid`.`total_pec` / (1 + `v_unpaid`.`rate` / 10000) / 100)
                  from ((`order_cart_service` `ocs_unpaid` join `orders` `o_unpaid`
                         on (`o_unpaid`.`id` = `ocs_unpaid`.`order_id`)) join `vat` `v_unpaid`
                        on (`v_unpaid`.`id` = `ocs_unpaid`.`vat_id`))
                  where `ocs_unpaid`.`service_id` = `s`.`id`
                    and `ocs_unpaid`.`total_pec` > 0
                    and `o_unpaid`.`status` = 'unpaid'
                    and `o_unpaid`.`client_type` <> 'group'
                    and `ocs_unpaid`.`cancelled_at` is null
                    and `o_unpaid`.`cancelled_at` is null), 0),
          2) AS `pec_unpaid_net`,

    -- Net paid amounts (both group and non-group)
    format(
        ifnull((select sum(`ocs_paid_net`.`total_net` / 100)
                from (`order_cart_service` `ocs_paid_net` join `orders` `o_paid_net`
                      on (`o_paid_net`.`id` = `ocs_paid_net`.`order_id`))
                where `ocs_paid_net`.`service_id` = `s`.`id`
                  and `ocs_paid_net`.`total_net` <> 0
                  and `o_paid_net`.`status` = 'paid'
                  and `o_paid_net`.`client_type` <> 'group'
                  and `ocs_paid_net`.`cancelled_at` is null
                  and `o_paid_net`.`cancelled_at` is null), 0) +
        ifnull((select sum(`ocs_group_paid`.`total_net` / 100)
                from (`order_cart_service` `ocs_group_paid` join `orders` `o_group_paid`
                      on (`o_group_paid`.`id` = `ocs_group_paid`.`order_id`))
                where `ocs_group_paid`.`service_id` = `s`.`id`
                  and `ocs_group_paid`.`total_net` <> 0
                  and `o_group_paid`.`status` = 'paid'
                  and `o_group_paid`.`client_type` = 'group'
                  and `ocs_group_paid`.`cancelled_at` is null
                  and `o_group_paid`.`cancelled_at` is null), 0),
        2) AS `paid_net`,

    -- Net unpaid amounts (both group and non-group)
    format(
        ifnull((select sum(`ocs_unpaid_net`.`total_net` / 100)
                from (`order_cart_service` `ocs_unpaid_net` join `orders` `o_unpaid_net`
                      on (`o_unpaid_net`.`id` = `ocs_unpaid_net`.`order_id`))
                where `ocs_unpaid_net`.`service_id` = `s`.`id`
                  and `ocs_unpaid_net`.`total_net` <> 0
                  and `o_unpaid_net`.`status` = 'unpaid'
                  and `o_unpaid_net`.`client_type` <> 'group'
                  and `ocs_unpaid_net`.`cancelled_at` is null
                  and `o_unpaid_net`.`cancelled_at` is null), 0) +
        ifnull((select sum(`ocs_group_unpaid`.`total_net` / 100)
                from (`order_cart_service` `ocs_group_unpaid` join `orders` `o_group_unpaid`
                      on (`o_group_unpaid`.`id` = `ocs_group_unpaid`.`order_id`))
                where `ocs_group_unpaid`.`service_id` = `s`.`id`
                  and `ocs_group_unpaid`.`total_net` <> 0
                  and `o_group_unpaid`.`status` = 'unpaid'
                  and `o_group_unpaid`.`client_type` = 'group'
                  and `ocs_group_unpaid`.`cancelled_at` is null
                  and `o_group_unpaid`.`cancelled_at` is null), 0),
        2) AS `unpaid_net`,

    -- Congress net - CORRECTED JOIN
    format(
        -- Non-group individual orders for congress participants
        ifnull((select sum(`ocs_congress`.`total_net` / 100)
                from ((`order_cart_service` `ocs_congress`
                      join `orders` `o_congress` on (`o_congress`.`id` = `ocs_congress`.`order_id`))
                      join `events_contacts` `ec_congress` on (`ec_congress`.`user_id` = `o_congress`.`client_id` AND `ec_congress`.`event_id` = `o_congress`.`event_id`))
                      join `participation_types` `pt_congress` on (`ec_congress`.`participation_type_id` = `pt_congress`.`id`)
                where `ocs_congress`.`service_id` = `s`.`id`
                  and (`ocs_congress`.`total_net` <> 0 OR `ocs_congress`.`total_pec` > 0)
                  and `o_congress`.`client_type` <> 'group'
                  and `pt_congress`.`group` = 'congress'
                  and `ocs_congress`.`cancelled_at` is null
                  and `o_congress`.`cancelled_at` is null), 0) +

        -- PEC amounts for congress participants
        ifnull((select sum(`ocs_congress_pec`.`total_pec` / (1 + `v_congress`.`rate` / 10000) / 100)
                from ((`order_cart_service` `ocs_congress_pec`
                      join `orders` `o_congress_pec` on (`o_congress_pec`.`id` = `ocs_congress_pec`.`order_id`))
                      join `events_contacts` `ec_congress_pec` on (`ec_congress_pec`.`user_id` = `o_congress_pec`.`client_id` AND `ec_congress_pec`.`event_id` = `o_congress_pec`.`event_id`))
                      join `participation_types` `pt_congress_pec` on (`ec_congress_pec`.`participation_type_id` = `pt_congress_pec`.`id`)
                      join `vat` `v_congress` on (`v_congress`.`id` = `ocs_congress_pec`.`vat_id`)
                where `ocs_congress_pec`.`service_id` = `s`.`id`
                  and `ocs_congress_pec`.`total_pec` > 0
                  and `o_congress_pec`.`client_type` <> 'group'
                  and `pt_congress_pec`.`group` = 'congress'
                  and `ocs_congress_pec`.`cancelled_at` is null
                  and `o_congress_pec`.`cancelled_at` is null), 0) +

        -- Group attributions for congress participants
        ifnull((select sum((`ocs_group_attr`.`total_net` / 100) * (`oa`.`quantity` /
                  (select COALESCE(sum(`oa_total`.`quantity`), 1)
                   from `order_attributions` `oa_total`
                   where `oa_total`.`order_id` = `o_group_attr`.`id`
                     and `oa_total`.`shoppable_type` = 'service'
                     and `oa_total`.`shoppable_id` = `s`.`id`)))
                from (((`order_cart_service` `ocs_group_attr`
                      join `orders` `o_group_attr` on (`o_group_attr`.`id` = `ocs_group_attr`.`order_id`))
                      join `order_attributions` `oa` on (`oa`.`order_id` = `o_group_attr`.`id`))
                      join `events_contacts` `ec_attr` on (`ec_attr`.`id` = `oa`.`event_contact_id`))
                      join `participation_types` `pt_attr` on (`ec_attr`.`participation_type_id` = `pt_attr`.`id`)
                where `ocs_group_attr`.`service_id` = `s`.`id`
                  and `oa`.`shoppable_type` = 'service'
                  and `oa`.`shoppable_id` = `s`.`id`
                  and `pt_attr`.`group` = 'congress'
                  and `o_group_attr`.`client_type` = 'group'
                  and `ocs_group_attr`.`cancelled_at` is null
                  and `o_group_attr`.`cancelled_at` is null), 0),
        2) AS `congress_net`,

    -- Industry net - CORRECTED JOIN
    format(
        -- Non-group individual orders for industry participants
        ifnull((select sum(`ocs_industry`.`total_net` / 100)
                from ((`order_cart_service` `ocs_industry`
                      join `orders` `o_industry` on (`o_industry`.`id` = `ocs_industry`.`order_id`))
                      join `events_contacts` `ec_industry` on (`ec_industry`.`user_id` = `o_industry`.`client_id` AND `ec_industry`.`event_id` = `o_industry`.`event_id`))
                      join `participation_types` `pt_industry` on (`ec_industry`.`participation_type_id` = `pt_industry`.`id`)
                where `ocs_industry`.`service_id` = `s`.`id`
                  and (`ocs_industry`.`total_net` <> 0 OR `ocs_industry`.`total_pec` > 0)
                  and `o_industry`.`client_type` <> 'group'
                  and `pt_industry`.`group` = 'industry'
                  and `ocs_industry`.`cancelled_at` is null
                  and `o_industry`.`cancelled_at` is null), 0) +

        -- PEC amounts for industry participants
        ifnull((select sum(`ocs_industry_pec`.`total_pec` / (1 + `v_industry`.`rate` / 10000) / 100)
                from ((`order_cart_service` `ocs_industry_pec`
                      join `orders` `o_industry_pec` on (`o_industry_pec`.`id` = `ocs_industry_pec`.`order_id`))
                      join `events_contacts` `ec_industry_pec` on (`ec_industry_pec`.`user_id` = `o_industry_pec`.`client_id` AND `ec_industry_pec`.`event_id` = `o_industry_pec`.`event_id`))
                      join `participation_types` `pt_industry_pec` on (`ec_industry_pec`.`participation_type_id` = `pt_industry_pec`.`id`)
                      join `vat` `v_industry` on (`v_industry`.`id` = `ocs_industry_pec`.`vat_id`)
                where `ocs_industry_pec`.`service_id` = `s`.`id`
                  and `ocs_industry_pec`.`total_pec` > 0
                  and `o_industry_pec`.`client_type` <> 'group'
                  and `pt_industry_pec`.`group` = 'industry'
                  and `ocs_industry_pec`.`cancelled_at` is null
                  and `o_industry_pec`.`cancelled_at` is null), 0) +

        -- Group attributions for industry participants
        ifnull((select sum((`ocs_group_attr`.`total_net` / 100) * (`oa`.`quantity` /
                  (select COALESCE(sum(`oa_total`.`quantity`), 1)
                   from `order_attributions` `oa_total`
                   where `oa_total`.`order_id` = `o_group_attr`.`id`
                     and `oa_total`.`shoppable_type` = 'service'
                     and `oa_total`.`shoppable_id` = `s`.`id`)))
                from (((`order_cart_service` `ocs_group_attr`
                      join `orders` `o_group_attr` on (`o_group_attr`.`id` = `ocs_group_attr`.`order_id`))
                      join `order_attributions` `oa` on (`oa`.`order_id` = `o_group_attr`.`id`))
                      join `events_contacts` `ec_attr` on (`ec_attr`.`id` = `oa`.`event_contact_id`))
                      join `participation_types` `pt_attr` on (`ec_attr`.`participation_type_id` = `pt_attr`.`id`)
                where `ocs_group_attr`.`service_id` = `s`.`id`
                  and `oa`.`shoppable_type` = 'service'
                  and `oa`.`shoppable_id` = `s`.`id`
                  and `pt_attr`.`group` = 'industry'
                  and `o_group_attr`.`client_type` = 'group'
                  and `ocs_group_attr`.`cancelled_at` is null
                  and `o_group_attr`.`cancelled_at` is null), 0),
        2) AS `industry_net`,

    -- Orators net - CORRECTED JOIN
    format(
        -- Non-group individual orders for orator participants
        ifnull((select sum(`ocs_orator`.`total_net` / 100)
                from ((`order_cart_service` `ocs_orator`
                      join `orders` `o_orator` on (`o_orator`.`id` = `ocs_orator`.`order_id`))
                      join `events_contacts` `ec_orator` on (`ec_orator`.`user_id` = `o_orator`.`client_id` AND `ec_orator`.`event_id` = `o_orator`.`event_id`))
                      join `participation_types` `pt_orator` on (`ec_orator`.`participation_type_id` = `pt_orator`.`id`)
                where `ocs_orator`.`service_id` = `s`.`id`
                  and (`ocs_orator`.`total_net` <> 0 OR `ocs_orator`.`total_pec` > 0)
                  and `o_orator`.`client_type` <> 'group'
                  and `pt_orator`.`group` = 'orator'
                  and `ocs_orator`.`cancelled_at` is null
                  and `o_orator`.`cancelled_at` is null), 0) +

        -- PEC amounts for orator participants
        ifnull((select sum(`ocs_orator_pec`.`total_pec` / (1 + `v_orator`.`rate` / 10000) / 100)
                from ((`order_cart_service` `ocs_orator_pec`
                      join `orders` `o_orator_pec` on (`o_orator_pec`.`id` = `ocs_orator_pec`.`order_id`))
                      join `events_contacts` `ec_orator_pec` on (`ec_orator_pec`.`user_id` = `o_orator_pec`.`client_id` AND `ec_orator_pec`.`event_id` = `o_orator_pec`.`event_id`))
                      join `participation_types` `pt_orator_pec` on (`ec_orator_pec`.`participation_type_id` = `pt_orator_pec`.`id`)
                      join `vat` `v_orator` on (`v_orator`.`id` = `ocs_orator_pec`.`vat_id`)
                where `ocs_orator_pec`.`service_id` = `s`.`id`
                  and `ocs_orator_pec`.`total_pec` > 0
                  and `o_orator_pec`.`client_type` <> 'group'
                  and `pt_orator_pec`.`group` = 'orator'
                  and `ocs_orator_pec`.`cancelled_at` is null
                  and `o_orator_pec`.`cancelled_at` is null), 0) +

        -- Group attributions for orator participants
        ifnull((select sum((`ocs_group_attr`.`total_net` / 100) * (`oa`.`quantity` /
                  (select COALESCE(sum(`oa_total`.`quantity`), 1)
                   from `order_attributions` `oa_total`
                   where `oa_total`.`order_id` = `o_group_attr`.`id`
                     and `oa_total`.`shoppable_type` = 'service'
                     and `oa_total`.`shoppable_id` = `s`.`id`)))
                from (((`order_cart_service` `ocs_group_attr`
                      join `orders` `o_group_attr` on (`o_group_attr`.`id` = `ocs_group_attr`.`order_id`))
                      join `order_attributions` `oa` on (`oa`.`order_id` = `o_group_attr`.`id`))
                      join `events_contacts` `ec_attr` on (`ec_attr`.`id` = `oa`.`event_contact_id`))
                      join `participation_types` `pt_attr` on (`ec_attr`.`participation_type_id` = `pt_attr`.`id`)
                where `ocs_group_attr`.`service_id` = `s`.`id`
                  and `oa`.`shoppable_type` = 'service'
                  and `oa`.`shoppable_id` = `s`.`id`
                  and `pt_attr`.`group` = 'orator'
                  and `o_group_attr`.`client_type` = 'group'
                  and `ocs_group_attr`.`cancelled_at` is null
                  and `o_group_attr`.`cancelled_at` is null), 0),
        2) AS `orators_net`,

    -- Unassigned amount calculation - For group orders only
    format(
        GREATEST(0, -- Ensure the value is never negative
            ifnull((
                -- Total of group orders (paid + unpaid)
                (select COALESCE(sum(`ocs_total_group`.`total_net` / 100), 0)
                 from (`order_cart_service` `ocs_total_group` join `orders` `o_total_group`
                      on (`o_total_group`.`id` = `ocs_total_group`.`order_id`))
                 where `ocs_total_group`.`service_id` = `s`.`id`
                   and `ocs_total_group`.`total_net` <> 0
                   and `o_total_group`.`client_type` = 'group'
                   and `ocs_total_group`.`cancelled_at` is null
                   and `o_total_group`.`cancelled_at` is null)
                -
                -- Minus total of attributed amounts
                (select COALESCE(sum((`ocs_attributed`.`total_net` / 100) * (`oa`.`quantity` /
                        (select COALESCE(sum(`oa_total`.`quantity`), 1)
                         from `order_attributions` `oa_total`
                         where `oa_total`.`order_id` = `o_attributed`.`id`
                           and `oa_total`.`shoppable_type` = 'service'
                           and `oa_total`.`shoppable_id` = `s`.`id`)
                    )), 0)
                 from (((`order_cart_service` `ocs_attributed`
                      join `orders` `o_attributed` on (`o_attributed`.`id` = `ocs_attributed`.`order_id`))
                      join `order_attributions` `oa` on (`oa`.`order_id` = `o_attributed`.`id`)))
                 where `ocs_attributed`.`service_id` = `s`.`id`
                   and `oa`.`shoppable_type` = 'service'
                   and `oa`.`shoppable_id` = `s`.`id`
                   and `o_attributed`.`client_type` = 'group'
                   and `ocs_attributed`.`cancelled_at` is null
                   and `o_attributed`.`cancelled_at` is null)
            ), 0)
        ),
    2) AS `net_unassigned`

from ((`event_sellable_service` `s` left join `event_sellable_service_prices` `esp`
   on (`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb`
  on (`sb`.`id` = `s`.`id`))
group by `s`.`id`;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_view AS
        select `s`.`id` AS `id`,`s`.`deleted_at` AS `deleted_at`,`s`.`event_id` AS `event_id`,json_unquote(json_extract(`s`.`title`,'$.fr')) AS `title`,date_format(`s`.`service_date`,'%d/%m/%Y') AS `service_date`,`s`.`published` AS `published`,`s`.`pec_eligible` AS `pec_eligible`,group_concat(distinct cast(`esp`.`price` / 100 as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices`,`sb`.`stock` AS `stock`,`sb`.`stock_label` AS `stock_label`,`sb`.`bookings_count` AS `bookings_count`,`sb`.`temp_bookings_count` AS `temp_bookings_count`,`sb`.`temp_front_bookings_count` AS `temp_front_bookings_count`,`sb`.`total_bookings_count` AS `total_bookings_count`,`sb`.`available` AS `available`,`sb`.`available_label` AS `available_label`,format(ifnull((select sum(`ocs_paid`.`total_pec` / (1 + `v_paid`.`rate` / 10000) / 100) from ((`order_cart_service` `ocs_paid` join `orders` `o_paid` on(`o_paid`.`id` = `ocs_paid`.`order_id`)) join `vat` `v_paid` on(`v_paid`.`id` = `ocs_paid`.`vat_id`)) where `ocs_paid`.`service_id` = `s`.`id` and `ocs_paid`.`total_pec` > 0 and `o_paid`.`status` = 'paid' and `ocs_paid`.`cancelled_at` is null and `o_paid`.`cancelled_at` is null),0),2) AS `pec_paid_net`,format(ifnull((select sum(`ocs_unpaid`.`total_pec` / (1 + `v_unpaid`.`rate` / 10000) / 100) from ((`order_cart_service` `ocs_unpaid` join `orders` `o_unpaid` on(`o_unpaid`.`id` = `ocs_unpaid`.`order_id`)) join `vat` `v_unpaid` on(`v_unpaid`.`id` = `ocs_unpaid`.`vat_id`)) where `ocs_unpaid`.`service_id` = `s`.`id` and `ocs_unpaid`.`total_pec` > 0 and `o_unpaid`.`status` = 'unpaid' and `ocs_unpaid`.`cancelled_at` is null and `o_unpaid`.`cancelled_at` is null),0),2) AS `pec_unpaid_net`,format(ifnull((select sum(`ocs_paid_net`.`total_net` / 100) from (`order_cart_service` `ocs_paid_net` join `orders` `o_paid_net` on(`o_paid_net`.`id` = `ocs_paid_net`.`order_id`)) where `ocs_paid_net`.`service_id` = `s`.`id` and `ocs_paid_net`.`total_net` <> 0 and `o_paid_net`.`status` = 'paid' and `ocs_paid_net`.`cancelled_at` is null and `o_paid_net`.`cancelled_at` is null),0),2) AS `paid_net`,format(ifnull((select sum(`ocs_unpaid_net`.`total_net` / 100) from (`order_cart_service` `ocs_unpaid_net` join `orders` `o_unpaid_net` on(`o_unpaid_net`.`id` = `ocs_unpaid_net`.`order_id`)) where `ocs_unpaid_net`.`service_id` = `s`.`id` and `ocs_unpaid_net`.`total_net` <> 0 and `o_unpaid_net`.`status` = 'unpaid' and `ocs_unpaid_net`.`cancelled_at` is null and `o_unpaid_net`.`cancelled_at` is null),0),2) AS `unpaid_net`,format(ifnull((select sum(`ocs_congress`.`total_net` / 100) from (((`order_cart_service` `ocs_congress` join `orders` `o_congress` on(`o_congress`.`id` = `ocs_congress`.`order_id`)) join `events_contacts` `ec_congress` on(`o_congress`.`client_id` = `ec_congress`.`id`)) join `participation_types` `pt_congress` on(`ec_congress`.`participation_type_id` = `pt_congress`.`id`)) where `ocs_congress`.`service_id` = `s`.`id` and `ocs_congress`.`total_net` <> 0 and `o_congress`.`client_type` <> 'group' and `pt_congress`.`group` = 'congress' and `ocs_congress`.`cancelled_at` is null and `o_congress`.`cancelled_at` is null),0),2) AS `congress_net`,format(ifnull((select sum(`ocs_industry`.`total_net` / 100) from (((`order_cart_service` `ocs_industry` join `orders` `o_industry` on(`o_industry`.`id` = `ocs_industry`.`order_id`)) join `events_contacts` `ec_industry` on(`o_industry`.`client_id` = `ec_industry`.`id`)) join `participation_types` `pt_industry` on(`ec_industry`.`participation_type_id` = `pt_industry`.`id`)) where `ocs_industry`.`service_id` = `s`.`id` and `ocs_industry`.`total_net` <> 0 and `o_industry`.`client_type` <> 'group' and `pt_industry`.`group` = 'industry' and `ocs_industry`.`cancelled_at` is null and `o_industry`.`cancelled_at` is null),0),2) AS `industry_net`,format(ifnull((select sum(`ocs_orator`.`total_net` / 100) from (((`order_cart_service` `ocs_orator` join `orders` `o_orator` on(`o_orator`.`id` = `ocs_orator`.`order_id`)) join `events_contacts` `ec_orator` on(`o_orator`.`client_id` = `ec_orator`.`id`)) join `participation_types` `pt_orator` on(`ec_orator`.`participation_type_id` = `pt_orator`.`id`)) where `ocs_orator`.`service_id` = `s`.`id` and `ocs_orator`.`total_net` <> 0 and `o_orator`.`client_type` <> 'group' and `pt_orator`.`group` = 'orator' and `ocs_orator`.`cancelled_at` is null and `o_orator`.`cancelled_at` is null),0),2) AS `orators_net` from ((`event_sellable_service` `s` left join `event_sellable_service_prices` `esp` on(`esp`.`event_sellable_service_id` = `s`.`id`)) left join `event_sellable_service_stock_view` `sb` on(`sb`.`id` = `s`.`id`)) group by `s`.`id` ");
    }
};
