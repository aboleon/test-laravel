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
        Schema::table('pax', function (Blueprint $table) {
            DB::statement(
                "CREATE OR REPLACE VIEW orders_view AS
            select `o`.`id`                                                                            AS `id`,
       `o`.`uuid`                                                                          AS `uuid`,
       `o`.`event_id`                                                                      AS `event_id`,
       `o`.`created_at`                                                                    AS `date`,
       `o`.`client_type`                                                                   AS `client_type`,
       `o`.`origin`                                                                        AS `origin`,
       `o`.`marker`                                                                        AS `marker`,
       `o`.`cancellation_status`                                                           AS `cancellation_status`,
       case
           when 'group' collate utf8mb4_unicode_ci = `o`.`client_type` then 'Groupe'
           when 'contact' collate utf8mb4_unicode_ci = `o`.`client_type` then 'Pax'
           when 'orator' collate utf8mb4_unicode_ci = `o`.`client_type` then 'Orateur' end AS `client_type_display`,
       `o`.`client_id`                                                                     AS `client_id`,
       `o`.`status`                                                                        AS `status`,
       `ft`.`transaction_id`                                                               AS `paybox_num_trans`,
       case
           when 'orator' collate utf8mb4_unicode_ci = `o`.`client_type` then '-'
           when 'paid' collate utf8mb4_unicode_ci = `o`.`status` then 'Soldée'
           when 'unpaid' collate utf8mb4_unicode_ci = `o`.`status` then 'Non-soldée' end   AS `status_display`,
       `oi`.`invoice_number`                                                               AS `invoice_number`,
       case
           when 'group' collate utf8mb4_unicode_ci = `o`.`client_type` then `g`.`name`
           when `o`.`client_type` in ('contact' collate utf8mb4_unicode_ci, 'orator' collate utf8mb4_unicode_ci)
               then concat(`u`.`last_name`, ' ', `u`.`first_name`) end                     AS `name`,
       format((`o`.`total_net` + `o`.`total_vat`) / 100, 2)                                AS `total`,
       format(coalesce(`p`.`payments_total`, 0) / 100, 2)                                  AS `payments_total`,
       format(`o`.`total_pec` / 100, 2)                                                    AS `total_pec`,
       `ec`.`order_cancellation`                                                           AS `order_cancellation`,
       case
           when `o`.`cancellation_request` is not null then `o`.`cancellation_request`
           when `ocs`.`cancellation_request` is not null then `ocs`.`cancellation_request`
           when `oca`.`cancellation_request` is not null then `oca`.`cancellation_request`
           else NULL end                                                                   AS `cancellation_request`,
       case
           when `o`.`cancelled_at` is not null then `o`.`cancelled_at`
           when `ocs`.`cancelled_at` is not null then `ocs`.`cancelled_at`
           when `oca`.`cancelled_at` is not null then `oca`.`cancelled_at`
           else NULL end                                                                   AS `cancelled_at`,
       case when `oi`.`order_id` is not null then 1 else NULL end                          AS `has_invoice`,
       case
           when 'orator' collate utf8mb4_unicode_ci = `o`.`client_type` then '-'
           when `oi`.`order_id` is not null then 'Oui'
           else 'Non' end                                                                  AS `has_invoice_display`,
       case
           when exists(select 1 from `order_cart_service` `ocs1` where `ocs1`.`order_id` = `o`.`id` limit 1) and
                exists(select 1 from `order_cart_accommodation` `oca1` where `oca1`.`order_id` = `o`.`id` limit 1)
               then 'Prestations, Hébergement'
           when exists(select 1 from `order_cart_service` `ocs1` where `ocs1`.`order_id` = `o`.`id` limit 1)
               then 'Prestations'
           when exists(select 1 from `order_cart_accommodation` `oca1` where `oca1`.`order_id` = `o`.`id` limit 1)
               then 'Hébergement'
           else '-' end                                                                    AS `contains`,
       case when `o`.`total_pec` > 0 then 'PEC' else NULL end                              AS `has_pec`,
       case
           when `o`.`amended_by_order_id` is not null then concat('modifiée par #', `o`.`amended_by_order_id`)
           else '' end                                                                     AS `amended_by_order`,
       case
           when `ec`.`order_cancellation` is not null then 'ne vient plus'
           when 'full' collate utf8mb4_unicode_ci = `o`.`cancellation_status` then 'complète'
           when 'partial' collate utf8mb4_unicode_ci = `o`.`cancellation_status` then 'partielle'
           else '' end                                                                     AS `cancellation_status_display`,
       concat(date_format(`o`.`created_at`, '%d '), case
                                                        when '01' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Janvier'
                                                        when '02' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Février'
                                                        when '03' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Mars'
                                                        when '04' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Avril'
                                                        when '05' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Mai'
                                                        when '06' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Juin'
                                                        when '07' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Juillet'
                                                        when '08' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Août'
                                                        when '09' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Septembre'
                                                        when '10' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Octobre'
                                                        when '11' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Novembre'
                                                        when '12' collate utf8mb4_unicode_ci = date_format(`o`.`created_at`, '%m')
                                                            then 'Décembre' end,
              date_format(`o`.`created_at`, ' %Y à %H:%i'))                                AS `date_display`
from ((((((((`orders` `o` left join `order_invoices` `oi`
             on (`oi`.`order_id` = `o`.`id`)) left join `front_transactions` `ft`
            on (`ft`.`order_id` = `o`.`id`)) left join `groups` `g`
           on ('group' collate utf8mb4_unicode_ci = `o`.`client_type` and
               `g`.`id` = `o`.`client_id`)) left join `users` `u`
          on (`o`.`client_type` in ('contact' collate utf8mb4_unicode_ci, 'orator' collate utf8mb4_unicode_ci) and
              `u`.`id` = `o`.`client_id`)) left join (select `op`.`order_id`    AS `order_id`,
                                                             sum(`op`.`amount`) AS `payments_total`
                                                      from `order_payments` `op`
                                                      group by `op`.`order_id`) `p`
         on (`p`.`order_id` = `o`.`id`)) left join `events_contacts` `ec`
        on (`o`.`client_type` in ('contact' collate utf8mb4_unicode_ci, 'orator' collate utf8mb4_unicode_ci) and
            `u`.`id` = `ec`.`user_id` and
            `o`.`event_id` = `ec`.`event_id`)) left join (select `ocs`.`order_id`                  AS `order_id`,
                                                                 max(`ocs`.`cancellation_request`) AS `cancellation_request`,
                                                                 max(`ocs`.`cancelled_at`)         AS `cancelled_at`
                                                          from `order_cart_service` `ocs`
                                                          group by `ocs`.`order_id`) `ocs`
       on (`ocs`.`order_id` = `o`.`id`)) left join (select `oca`.`order_id`                  AS `order_id`,
                                                           max(`oca`.`cancellation_request`) AS `cancellation_request`,
                                                           max(`oca`.`cancelled_at`)         AS `cancelled_at`
                                                    from `order_cart_accommodation` `oca`
                                                    group by `oca`.`order_id`) `oca` on (`oca`.`order_id` = `o`.`id`))
where `o`.`parent_id` is null
group by `o`.`id`",
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pax', function (Blueprint $table) {
            //
        });
    }
};
