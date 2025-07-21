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
        DB::statement("CREATE OR REPLACE VIEW event_deposits_view AS

SELECT `ed`.`id` AS `id`,
       `ed`.`order_id` AS `order_id`,
       `o`.`uuid` AS `uuid`,
       `ed`.`event_id` AS `event_id`,
       `ed`.`shoppable_type` AS `shoppable_type`,
       `ed`.`shoppable_label` AS `shoppable_label`,
       `ed`.`total_net` AS `total_net`,
       `ed`.`event_contact_id` AS `event_contact_id`,
       `ed`.`status` AS `status`,
       `ed`.`reimbursed_at` AS `reimbursed_at`,
       CASE WHEN `oi`.`order_id` IS NOT NULL THEN 1 ELSE NULL END AS `has_invoice`,
       CASE WHEN `e`.`ends` < CURDATE() AND `ec`.`is_attending` IS NULL THEN 1 ELSE 0 END AS `is_attending_expired`,
       `ed`.`total_net` + `ed`.`total_vat` AS `total_ttc`,
       DATE_FORMAT(`ed`.`created_at`, '%d/%m/%Y %H:%i:%s') AS `date_fr`,
       CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `beneficiary_name`,
       `pc`.`id` AS `payment_call_id`,
       CASE
           WHEN `ed`.`shoppable_type` = 'grantdeposit' THEN 'PEC'
           ELSE 'Prestation'
       END AS `type`
FROM ((((((`event_deposits` `ed`
          LEFT JOIN `orders` `o` ON (`ed`.`order_id` = `o`.`id`))
         LEFT JOIN `order_invoices` `oi` ON (`oi`.`order_id` = `o`.`id`))
        LEFT JOIN `events_contacts` `ec` ON (`ed`.`event_contact_id` = `ec`.`id`))
       LEFT JOIN `users` `u` ON (`ec`.`user_id` = `u`.`id`))
      LEFT JOIN `events` `e` ON (`ed`.`event_id` = `e`.`id`))
     LEFT JOIN `payment_call` `pc` ON (`pc`.`shoppable_type` LIKE '%EventDeposit' AND `pc`.`shoppable_id` = `ed`.`id`))
WHERE `ed`.`status` <> 'temp'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_deposits_view AS
        select `ed`.`id`                                                                          AS `id`,
       `ed`.`order_id`                                                                    AS `order_id`,
       `o`.`uuid`                                                                         AS `uuid`,
       `ed`.`event_id`                                                                    AS `event_id`,
       `ed`.`shoppable_type`                                                              AS `shoppable_type`,
       `ed`.`shoppable_label`                                                             AS `shoppable_label`,
       `ed`.`total_net`                                                                   AS `total_net`,
       `ed`.`event_contact_id`                                                            AS `event_contact_id`,
       `ed`.`status`                                                                      AS `status`,
       `ed`.`reimbursed_at`                                                               AS `reimbursed_at`,
       case when `oi`.`order_id` is not null then 1 else NULL end                         AS `has_invoice`,
       case when `e`.`ends` < curdate() and `ec`.`is_attending` is null then 1 else 0 end AS `is_attending_expired`,
       `ed`.`total_net` + `ed`.`total_vat`                                                AS `total_ttc`,
       date_format(`ed`.`created_at`, '%d/%m/%Y %H:%i:%s')                                AS `date_fr`,
       concat(`u`.`first_name`, ' ', `u`.`last_name`)                                     AS `beneficiary_name`,
       `pc`.`id`                                                                          AS `payment_call_id`
from ((((((`event_deposits` `ed` left join `orders` `o` on (`ed`.`order_id` = `o`.`id`)) left join `order_invoices` `oi`
          on (`oi`.`order_id` = `o`.`id`)) left join `events_contacts` `ec`
         on (`ed`.`event_contact_id` = `ec`.`id`)) left join `users` `u`
        on (`ec`.`user_id` = `u`.`id`)) left join `events` `e`
       on (`ed`.`event_id` = `e`.`id`)) left join `payment_call` `pc`
      on (`pc`.`shoppable_type` like '%EventDeposit' and `pc`.`shoppable_id` = `ed`.`id`))
where `ed`.`status` <> 'temp'

        ");
    }
};
