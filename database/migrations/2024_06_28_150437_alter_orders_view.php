<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW orders_view AS

        SELECT
  `o`.`id` AS `id`,
  `o`.`event_id` AS `event_id`,
  `o`.`created_at` AS `date`,
  `o`.`client_type` AS `client_type`,
  `o`.`origin` AS `origin`,
  `o`.`marker` AS `marker`,
  CASE
    WHEN 'group' COLLATE utf8mb4_unicode_ci = `o`.`client_type` THEN 'Groupe'
    WHEN 'contact' COLLATE utf8mb4_unicode_ci = `o`.`client_type` THEN 'Participant'
  END AS `client_type_display`,
  `o`.`client_id` AS `client_id`,
  `o`.`status` AS `status`,
  `o`.`paybox_num_trans` AS `paybox_num_trans`,
  CASE
    WHEN 'paid' COLLATE utf8mb4_unicode_ci = `o`.`status` THEN 'Soldée'
    WHEN 'unpaid' COLLATE utf8mb4_unicode_ci = `o`.`status` THEN 'Non-soldée'
  END AS `status_display`,
  `oi`.`invoice_number` AS `invoice_number`,
  CASE
    WHEN 'group' COLLATE utf8mb4_unicode_ci = `o`.`client_type` THEN `g`.`name`
    WHEN 'contact' COLLATE utf8mb4_unicode_ci = `o`.`client_type` THEN CONCAT(`u`.`last_name`, ' ', `u`.`first_name`)
  END AS `name`,
  FORMAT((`o`.`total_net` + `o`.`total_vat`) / 100, 2) AS `total`,
  FORMAT(COALESCE(`p`.`payments_total`, 0) / 100, 2) AS `payments_total`,
  FORMAT(`o`.`total_pec` / 100, 2) AS `total_pec`,
  `ec`.`order_cancellation` AS `order_cancellation`,
  CASE WHEN `oi`.`order_id` IS NOT NULL THEN 1 ELSE NULL END AS `has_invoice`,
  CASE WHEN `oi`.`order_id` IS NOT NULL THEN 'Oui' ELSE 'Non' END AS `has_invoice_display`,
  CASE
    WHEN EXISTS(SELECT 1 FROM `order_cart_service` `ocs` WHERE `ocs`.`order_id` = `o`.`id` LIMIT 1)
      AND EXISTS(SELECT 1 FROM `order_cart_accommodation` `oca` WHERE `oca`.`order_id` = `o`.`id` LIMIT 1)
      THEN 'Prestations, Hébergement'
    WHEN EXISTS(SELECT 1 FROM `order_cart_service` `ocs` WHERE `ocs`.`order_id` = `o`.`id` LIMIT 1)
      THEN 'Prestations'
    WHEN EXISTS(SELECT 1 FROM `order_cart_accommodation` `oca` WHERE `oca`.`order_id` = `o`.`id` LIMIT 1)
      THEN 'Hébergement'
    ELSE '-'
  END AS `contains`,
  CASE
    WHEN `o`.`total_pec` > 0 THEN 'PEC'
    ELSE NULL
  END AS `has_pec`,
  CONCAT(
    DATE_FORMAT(`o`.`created_at`, '%d '),
    CASE
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '01' COLLATE utf8mb4_unicode_ci THEN 'Janvier'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '02' COLLATE utf8mb4_unicode_ci THEN 'Février'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '03' COLLATE utf8mb4_unicode_ci THEN 'Mars'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '04' COLLATE utf8mb4_unicode_ci THEN 'Avril'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '05' COLLATE utf8mb4_unicode_ci THEN 'Mai'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '06' COLLATE utf8mb4_unicode_ci THEN 'Juin'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '07' COLLATE utf8mb4_unicode_ci THEN 'Juillet'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '08' COLLATE utf8mb4_unicode_ci THEN 'Août'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '09' COLLATE utf8mb4_unicode_ci THEN 'Septembre'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '10' COLLATE utf8mb4_unicode_ci THEN 'Octobre'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '11' COLLATE utf8mb4_unicode_ci THEN 'Novembre'
      WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '12' COLLATE utf8mb4_unicode_ci THEN 'Décembre'
    END,
    DATE_FORMAT(`o`.`created_at`, ' %Y à %H:%i')
  ) AS `date_display`
FROM
  `orders` `o`
  LEFT JOIN `order_invoices` `oi` ON (`oi`.`order_id` = `o`.`id`)
  LEFT JOIN `front_transactions` `ft` ON (`ft`.`order_uuid` = `o`.`uuid`)
  LEFT JOIN `groups` `g` ON (`o`.`client_type` = 'group' AND `g`.`id` = `o`.`client_id`)
  LEFT JOIN `users` `u` ON (`o`.`client_type` = 'contact' AND `u`.`id` = `o`.`client_id`)
  LEFT JOIN (
    SELECT
      `op`.`order_id` AS `order_id`,
      SUM(`op`.`amount`) AS `payments_total`
    FROM
      `order_payments` `op`
    GROUP BY
      `op`.`order_id`
  ) `p` ON (`p`.`order_id` = `o`.`id`)
  LEFT JOIN `events_contacts` `ec` ON (`o`.`client_type` = 'contact' AND `u`.`id` = `ec`.`user_id` AND `o`.`event_id` = `ec`.`event_id`);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW orders_view AS
        select `o`.`id` AS `id`,`o`.`event_id` AS `event_id`,`o`.`created_at` AS `date`,`o`.`client_type` AS `client_type`,`o`.`origin` AS `origin`,`o`.`marker` AS `marker`,case when 'group' = `o`.`client_type` then 'Groupe' when 'contact' = `o`.`client_type` then 'Participant' end AS `client_type_display`,`o`.`client_id` AS `client_id`,`o`.`status` AS `status`,`o`.`paybox_num_trans` AS `paybox_num_trans`,case when 'paid' = `o`.`status` then 'Soldée' when 'unpaid' = `o`.`status` then 'Non-soldée' end AS `status_display`,`oi`.`invoice_number` AS `invoice_number`,case when 'group' = `o`.`client_type` then `g`.`name` when 'contact' = `o`.`client_type` then concat(`u`.`last_name`,' ',`u`.`first_name`) end AS `name`,format((`o`.`total_net` + `o`.`total_vat`) / 100,2) AS `total`,format(coalesce(`p`.`payments_total`,0) / 100,2) AS `payments_total`,format(`o`.`total_pec` / 100,2) AS `total_pec`,`ec`.`order_cancellation` AS `order_cancellation`,case when `oi`.`order_id` is not null then 1 else NULL end AS `has_invoice`,case when `oi`.`order_id` is not null then 'Oui' else 'Non' end AS `has_invoice_display`,case when exists(select 1 from `order_cart_service` `ocs` where `ocs`.`order_id` = `o`.`id` limit 1) and exists(select 1 from `order_cart_accommodation` `oca` where `oca`.`order_id` = `o`.`id` limit 1) then 'Prestations, Hébergement' when exists(select 1 from `order_cart_service` `ocs` where `ocs`.`order_id` = `o`.`id` limit 1) then 'Prestations' when exists(select 1 from `order_cart_accommodation` `oca` where `oca`.`order_id` = `o`.`id` limit 1) then 'Hébergement' else '-' end AS `contains` from ((((((`orders` `o` left join `order_invoices` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `front_transactions` `ft` on(`ft`.`order_uuid` = `o`.`uuid`)) left join `groups` `g` on(`o`.`client_type` = 'group' and `g`.`id` = `o`.`client_id`)) left join `users` `u` on(`o`.`client_type` = 'contact' and `u`.`id` = `o`.`client_id`)) left join (select `op`.`order_id` AS `order_id`,sum(`op`.`amount`) AS `payments_total` from `order_payments` `op` group by `op`.`order_id`) `p` on(`p`.`order_id` = `o`.`id`)) left join `events_contacts` `ec` on(`o`.`client_type` = 'contact' and `u`.`id` = `ec`.`user_id` and `o`.`event_id` = `ec`.`event_id`)) ");
    }
};
