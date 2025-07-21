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
        select 'order'                                               AS `type`,
       `o`.`event_id`                                        AS `event_id`,
       `o`.`id`                                              AS `order_id`,
       `o`.`uuid`                                            AS `uuid`,
       `o`.`client_id`                                       AS `client_id`,
       `o`.`client_type`                                     AS `client_type`,
       `o`.`created_at`                                      AS `date`,
       `o`.`total_net`                                       AS `total_net`,
       `o`.`total_vat`                                       AS `total_vat`,
       `o`.`total_net` + `o`.`total_vat`                     AS `total_ttc`,
       coalesce(sum(`pd`.`total_net` + `pd`.`total_vat`), 0) AS `total_pec`,
       coalesce(`pd`.`total_net`, 0)                         AS `total_pec_net`,
       coalesce(`pd`.`total_vat`, 0)                         AS `total_pec_vat`,
       `oi`.`id`                                             AS `order_invoice_id`
from ((`orders` `o` left join `order_invoices` `oi` on (`oi`.`order_id` = `o`.`id`)) left join `pec_distribution` `pd`
      on (`o`.`id` = `pd`.`order_id`))
where `pd`.type != 'processing_fee'
group by `o`.`id`
union all
select 'refund'                                                                                AS `type`,
       `rv`.`event_id`                                                                         AS `event_id`,
       `rv`.`order_id`                                                                         AS `order_id`,
       `rv`.`uuid`                                                                             AS `uuid`,
       `rv`.`client_id`                                                                        AS `client_id`,
       `rv`.`client_type`                                                                      AS `client_type`,
       `rv`.`created_at_raw`                                                                   AS `date`,
       cast(`rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed)                    AS `total_net`,
       cast(`rv`.`total_raw` - `rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed) AS `total_vat`,
       `rv`.`total_raw`                                                                        AS `total_ttc`,
       0                                                                                       AS `total_pec`,
       0                                                                                       AS `total_pec_net`,
       0                                                                                       AS `total_pec_vat`,
       NULL                                                                                    AS `order_invoice_id`
from `order_refunds_view` `rv`

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW front_my_orders_view AS
        select 'order'                                               AS `type`,
       `o`.`event_id`                                        AS `event_id`,
       `o`.`id`                                              AS `order_id`,
       `o`.`uuid`                                            AS `uuid`,
       `o`.`client_id`                                       AS `client_id`,
       `o`.`client_type`                                     AS `client_type`,
       `o`.`created_at`                                      AS `date`,
       `o`.`total_net`                                       AS `total_net`,
       `o`.`total_vat`                                       AS `total_vat`,
       `o`.`total_net` + `o`.`total_vat`                     AS `total_ttc`,
       coalesce(sum(`pd`.`total_net` + `pd`.`total_vat`), 0) AS `total_pec`,
       coalesce(`pd`.`total_net`, 0)                         AS `total_pec_net`,
       coalesce(`pd`.`total_vat`, 0)                         AS `total_pec_vat`,
       `oi`.`id`                                             AS `order_invoice_id`
from ((`orders` `o` left join `order_invoices` `oi` on (`oi`.`order_id` = `o`.`id`)) left join `pec_distribution` `pd`
      on (`o`.`id` = `pd`.`order_id`))
group by `o`.`id`
union all
select 'refund'                                                                                AS `type`,
       `rv`.`event_id`                                                                         AS `event_id`,
       `rv`.`order_id`                                                                         AS `order_id`,
       `rv`.`uuid`                                                                             AS `uuid`,
       `rv`.`client_id`                                                                        AS `client_id`,
       `rv`.`client_type`                                                                      AS `client_type`,
       `rv`.`created_at_raw`                                                                   AS `date`,
       cast(`rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed)                    AS `total_net`,
       cast(`rv`.`total_raw` - `rv`.`total_raw` / (1 + `rv`.`vat_rate_raw` / 10000) as signed) AS `total_vat`,
       `rv`.`total_raw`                                                                        AS `total_ttc`,
       0                                                                                       AS `total_pec`,
       0                                                                                       AS `total_pec_net`,
       0                                                                                       AS `total_pec_vat`,
       NULL                                                                                    AS `order_invoice_id`
from `order_refunds_view` `rv`

        ");
    }
};
