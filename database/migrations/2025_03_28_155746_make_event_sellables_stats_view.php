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
        DB::statement("DROP VIEW IF EXISTS event_sellables_orders");
        DB::statement("CREATE OR REPLACE VIEW `event_sellables_stats_view` AS
select `ocs`.`order_id`                                               AS `order_id`,
       `o`.`event_id`                                                 AS `event_id`,
       `o`.`client_type`                                              AS `client_type`,
       `o`.`client_id`                                                AS `client_id`,
       `ocs`.`service_id`                                             AS `service_id`,
       `ocs`.`quantity`                                               AS `quantity`,
       `ocs`.`cancelled_at`                                           AS `cart_canceled`,
       format(`ocs`.`total_net` / 100.0, 2)                           AS `total_net`,
       format(`ocs`.`total_vat` / 100.0, 2)                           AS `total_vat`,
       format((`ocs`.`total_net` + `ocs`.`total_vat`) / 100.0, 2)     AS `total`,
       format(`ocs`.`total_pec` / 100.0, 2)                           AS `total_pec`,
       `o`.`cancelled_at`                                             AS `order_cancelled_at`,
       case
           when `o`.`client_type` = 'group' then `eg`.`id`
           else `ec`.`id` end AS `dashboard_id`,
       case
           when `o`.`client_type` = 'group' then `g`.`name`
           else concat_ws(' ', `u`.`first_name`, `u`.`last_name`) end AS `name`,
       case
           when `ocs`.`cancelled_at` is not null or `o`.`cancelled_at` is not null then 1
           else 0 end                                                 AS `cancelled`,
       coalesce(`ocs`.`cancelled_at`, `o`.`cancelled_at`)             AS `cancelled_date`,
       case
           when `ocs`.`cancelled_at` is not null or `o`.`cancelled_at` is not null then 'Annulation'
           else 'Ok' end                                              AS `status`
from ((((`order_cart_service` `ocs` join `orders` `o` on (`o`.`id` = `ocs`.`order_id`))
    left join `users` `u` on (`u`.`id` = `o`.`client_id` and `o`.`client_type` <> 'group'))
    left join `groups` `g` on (`g`.`id` = `o`.`client_id` and `o`.`client_type` = 'group'))
    left join `events_contacts` `ec` on (`ec`.`user_id` = `u`.`id` and `ec`.`event_id` = `o`.`event_id`)
    left join `event_groups` `eg` on (`g`.`id` = `eg`.`group_id` and `eg`.`event_id` = `o`.`event_id`))

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement("DROP VIEW IF EXISTS event_sellables_stats_view");
        DB::statement("CREATE OR REPLACE VIEW `event_sellables_orders` AS
select `ocs`.`order_id` AS `order_id`,`o`.`event_id` AS `event_id`,`ocs`.`service_id` AS `service_id`,`ocs`.`quantity` AS `quantity`,`ocs`.`cancelled_at` AS `cart_canceled`,`o`.`cancelled_at` AS `order_cancelled_at`,`o`.`client_type` AS `client_type`,case when `o`.`client_type` = 'group' then `g`.`name` else concat_ws(' ',`u`.`first_name`,`u`.`last_name`) end AS `name`,`ec`.`id` AS `event_contact_id`,case when `ocs`.`cancelled_at` is not null or `o`.`cancelled_at` is not null then 1 else 0 end AS `cancelled`,coalesce(`ocs`.`cancelled_at`,`o`.`cancelled_at`) AS `cancelled_date`,case when `ocs`.`cancelled_at` is not null or `o`.`cancelled_at` is not null then 'Annulation' else 'Ok' end AS `status` from ((((`order_cart_service` `ocs` join `orders` `o` on(`o`.`id` = `ocs`.`order_id`)) left join `users` `u` on(`u`.`id` = `o`.`client_id` and `o`.`client_type` <> 'group')) left join `groups` `g` on(`g`.`id` = `o`.`client_id` and `o`.`client_type` = 'group')) left join `events_contacts` `ec` on(`ec`.`user_id` = `u`.`id` and `ec`.`event_id` = `o`.`event_id`))
        ");
    }
};
