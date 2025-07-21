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
        DB::statement("CREATE OR REPLACE VIEW orders_view AS
        select
    `o`.`id` AS `id`,
    `o`.`event_id` AS `event_id`,
    `o`.`created_at` AS `date`,
    `o`.`client_type` AS `client_type`,
    `o`.`origin` AS `origin`,
    `o`.`marker` AS `marker`,
    case
        when 'group' = `o`.`client_type` then 'Groupe'
        when 'contact' = `o`.`client_type` then 'Participant'
    end AS `client_type_display`,
    `o`.`client_id` AS `client_id`,
    `o`.`status` AS `status`,
    `o`.`paybox_num_trans` AS `paybox_num_trans`,
    case
        when 'paid' = `o`.`status` then 'Soldée'
        when 'unpaid' = `o`.`status` then 'Non-soldée'
    end AS `status_display`,
    `oi`.`invoice_number` AS `invoice_number`,
    case
        when 'group' = `o`.`client_type` then `g`.`name`
        when 'contact' = `o`.`client_type` then concat(`u`.`last_name`, ' ', `u`.`first_name`)
    end AS `name`,
    format((`o`.`total_net` + `o`.`total_vat`) / 100, 2) AS `total`,
    format(coalesce(`p`.`payments_total`, 0) / 100, 2) AS `payments_total`,
    format(`o`.`total_pec` / 100, 2) AS `total_pec`,
    `ec`.`order_cancellation` AS `order_cancellation`,
    case
        when `oi`.`order_id` is not null then 1
        else NULL
    end AS `has_invoice`,
    case
        when `oi`.`order_id` is not null then 'Oui'
        else 'Non'
    end AS `has_invoice_display`,
    case
        when exists(select 1
                    from `order_cart_service` `ocs`
                    where `ocs`.`order_id` = `o`.`id` limit 1)
             and exists(select 1
                        from `order_cart_accommodation` `oca`
                        where `oca`.`order_id` = `o`.`id` limit 1)
            then 'Prestations, Hébergement'
        when exists(select 1
                    from `order_cart_service` `ocs`
                    where `ocs`.`order_id` = `o`.`id` limit 1)
            then 'Prestations'
        when exists(select 1
                    from `order_cart_accommodation` `oca`
                    where `oca`.`order_id` = `o`.`id` limit 1)
            then 'Hébergement'
        else '-'
    end AS `contains`
from
    ((((((`orders` `o`
    left join `order_invoices` `oi` on (`oi`.`order_id` = `o`.`id`))
    left join `front_transactions` `ft` on (`ft`.`order_uuid` = `o`.`uuid`))
    left join `groups` `g` on (`o`.`client_type` = 'group' and `g`.`id` = `o`.`client_id`))
    left join `users` `u` on (`o`.`client_type` = 'contact' and `u`.`id` = `o`.`client_id`))
    left join (select `op`.`order_id` AS `order_id`,
                      sum(`op`.`amount`) AS `payments_total`
               from `order_payments` `op`
               group by `op`.`order_id`) `p` on (`p`.`order_id` = `o`.`id`))
    left join `events_contacts` `ec` on (`o`.`client_type` = 'contact' and `u`.`id` = `ec`.`user_id` and `o`.`event_id` = `ec`.`event_id`))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
