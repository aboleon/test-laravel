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
        DB::statement("CREATE OR REPLACE VIEW front_my_orders_view AS
        select `o`.type                                              AS `type`,
       `o`.`event_id`                                        AS `event_id`,
       `o`.`id`                                              AS `order_id`,
       `o`.`status`                                          AS `status`,
       `o`.`uuid`                                            AS `uuid`,
       `o`.`client_id`                                       AS `client_id`,
       `o`.`client_type`                                     AS `client_type`,
       `o`.`created_at`                                      AS `date`,
       `o`.`total_net`                                       AS `total_net`,
       `o`.`total_vat`                                       AS `total_vat`,
       `o`.`total_net` + `o`.`total_vat`                     AS `total_ttc`,
       coalesce(sum(`pd`.`total_net` + `pd`.`total_vat`), 0) AS `total_pec`,
       coalesce(sum(`pd`.`total_net`), 0)                    AS `total_pec_net`,
       coalesce(sum(`pd`.`total_vat`), 0)                    AS `total_pec_vat`,
       `oi`.`id`                                             AS `order_invoice_id`
from ((`orders` `o` left join `order_invoices` `oi` on (`oi`.`order_id` = `o`.`id`))
    left join `pec_distribution` `pd` on (`o`.`id` = `pd`.`order_id` and `pd`.`type` <> 'processing_fee'))
where o.type = 'order'
group by `o`.`id`, `oi`.`id`
union all
select CASE WHEN ed.shoppable_type = 'grantdeposit' THEN 'grantdeposit' ELSE 'deposit' END AS type,
       `ed`.`event_id`                                                                     AS `event_id`,
       `ed`.`order_id`                                                                     AS `order_id`,
       `ed`.`status`                                                                       AS `status`,
       `edo`.`uuid`                                                                        AS `uuid`,
       `edo`.`client_id`                                                                   AS `client_id`,
       `edo`.`client_type`                                                                 AS `client_type`,
       `edo`.`created_at`                                                                  AS `date`,
       `edo`.`total_net`                                                                   AS `total_net`,
       `edo`.`total_vat`                                                                   AS `total_vat`,
       `edo`.`total_net` + `edo`.`total_vat`                                               AS `total_ttc`,
       0                                                                                   AS `total_pec`,
       0                                                                                   AS `total_pec_net`,
       0                                                                                   AS `total_pec_vat`,
       `edoi`.`id`                                                                         AS `order_invoice_id`
from `event_deposits` `ed`
         join orders edo on ed.order_id = edo.id
         left join `order_invoices` `edoi` on `ed`.`order_id` = `edoi`.`order_id`
where ed.status != 'temp'
group by `ed`.`order_id`, `edoi`.`order_id`
union all
select 'refund'                                                                                AS `type`,
       `rv`.`event_id`                                                                         AS `event_id`,
       `rv`.`order_id`                                                                         AS `order_id`,
       'paid'                                                                                  AS `status`,
       `rv`.`uuid`                                                                             AS `uuid`,
       `rv`.`client_id`                                                                        AS `client_id`,
       `rv`.`client_type`                                                                      AS `client_type`,
       `rv`.`created_at_raw`                                                                   AS `created_at_raw`,
       cast(`rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed)                    AS `Name_exp_8`,
       cast(`rv`.`total_raw` - `rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed) AS `Name_exp_9`,
       `rv`.`total_raw`                                                                        AS `total_raw`,
       0                                                                                       AS `0`,
       0                                                                                       AS `0`,
       0                                                                                       AS `0`,
       NULL                                                                                    AS `NULL`
from `order_refunds_view` `rv`
order by order_id desc

");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW front_my_orders_view AS select 'order' AS `type`,`o`.`event_id` AS `event_id`,`o`.`id` AS `order_id`,`o`.`uuid` AS `uuid`,`o`.`client_id` AS `client_id`,`o`.`client_type` AS `client_type`,`o`.`created_at` AS `date`,`o`.`total_net` AS `total_net`,`o`.`total_vat` AS `total_vat`,`o`.`total_net` + `o`.`total_vat` AS `total_ttc`,coalesce(sum(`pd`.`total_net` + `pd`.`total_vat`),0) AS `total_pec`,coalesce(sum(`pd`.`total_net`),0) AS `total_pec_net`,coalesce(sum(`pd`.`total_vat`),0) AS `total_pec_vat`,`oi`.`id` AS `order_invoice_id` from ((`orders` `o` left join `order_invoices` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `pec_distribution` `pd` on(`o`.`id` = `pd`.`order_id` and `pd`.`type` <> 'processing_fee')) group by `o`.`id`,`oi`.`id` union all select 'refund' AS `refund`,`rv`.`event_id` AS `event_id`,`rv`.`order_id` AS `order_id`,`rv`.`uuid` AS `uuid`,`rv`.`client_id` AS `client_id`,`rv`.`client_type` AS `client_type`,`rv`.`created_at_raw` AS `created_at_raw`,cast(`rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed) AS `Name_exp_8`,cast(`rv`.`total_raw` - `rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed) AS `Name_exp_9`,`rv`.`total_raw` AS `total_raw`,0 AS `0`,0 AS `0`,0 AS `0`,NULL AS `NULL` from `order_refunds_view` `rv` ");
    }
};
