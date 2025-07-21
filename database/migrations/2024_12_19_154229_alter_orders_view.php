<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW orders_view AS
SELECT
    `o`.`id` AS `id`,
    `o`.`uuid` AS `uuid`,
    `o`.`event_id` AS `event_id`,
    `o`.`created_at` AS `date`,
    `o`.`client_type` AS `client_type`,
    `o`.`origin` AS `origin`,
    `o`.`marker` AS `marker`,
    CASE
        WHEN `o`.`client_type` = 'group' THEN `g`.`name`
        WHEN `o`.`client_type` IN ('contact', 'orator') THEN CONCAT(`u`.`last_name`, ' ', `u`.`first_name`)
        ELSE NULL
    END AS `name`,
    `o`.`client_id` AS `client_id`,
    `o`.`status` AS `status`,
    `ft`.`transaction_id` AS `paybox_num_trans`,
    CASE
        WHEN `o`.`client_type` = 'orator' THEN '-'
        WHEN `o`.`status` = 'paid' THEN 'Soldée'
        WHEN `o`.`status` = 'unpaid' THEN 'Non-soldée'
        ELSE NULL
    END AS `status_display`,
    `oi`.`invoice_number` AS `invoice_number`,
    FORMAT((`o`.`total_net` + `o`.`total_vat`) / 100, 2) AS `total`,
    FORMAT(COALESCE(`p`.`payments_total`, 0) / 100, 2) AS `payments_total`,
    FORMAT(`o`.`total_pec` / 100, 2) AS `total_pec`,
    `ec`.`order_cancellation` AS `order_cancellation`,
    CASE
        WHEN `o`.`cancellation_request` IS NOT NULL THEN `o`.`cancellation_request`
        WHEN `ocs`.`cancellation_request` IS NOT NULL THEN `ocs`.`cancellation_request`
        WHEN `oca`.`cancellation_request` IS NOT NULL THEN `oca`.`cancellation_request`
        ELSE NULL
    END AS `cancellation_request`,
    CASE
        WHEN `o`.`cancelled_at` IS NOT NULL THEN `o`.`cancelled_at`
        WHEN `ocs`.`cancelled_at` IS NOT NULL THEN `ocs`.`cancelled_at`
        WHEN `oca`.`cancelled_at` IS NOT NULL THEN `oca`.`cancelled_at`
        ELSE NULL
    END AS `cancelled_at`,
    CASE
        WHEN `oi`.`order_id` IS NOT NULL THEN 1
        ELSE NULL
    END AS `has_invoice`,
    CASE
        WHEN `o`.`client_type` = 'orator' THEN '-'
        WHEN `oi`.`order_id` IS NOT NULL THEN 'Oui'
        ELSE 'Non'
    END AS `has_invoice_display`,
    CASE
        WHEN EXISTS (SELECT 1 FROM `order_cart_service` `ocs1` WHERE `ocs1`.`order_id` = `o`.`id` LIMIT 1)
             AND EXISTS (SELECT 1 FROM `order_cart_accommodation` `oca1` WHERE `oca1`.`order_id` = `o`.`id` LIMIT 1)
        THEN 'Prestations, Hébergement'
        WHEN EXISTS (SELECT 1 FROM `order_cart_service` `ocs1` WHERE `ocs1`.`order_id` = `o`.`id` LIMIT 1)
        THEN 'Prestations'
        WHEN EXISTS (SELECT 1 FROM `order_cart_accommodation` `oca1` WHERE `oca1`.`order_id` = `o`.`id` LIMIT 1)
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
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '01' THEN 'Janvier'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '02' THEN 'Février'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '03' THEN 'Mars'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '04' THEN 'Avril'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '05' THEN 'Mai'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '06' THEN 'Juin'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '07' THEN 'Juillet'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '08' THEN 'Août'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '09' THEN 'Septembre'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '10' THEN 'Octobre'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '11' THEN 'Novembre'
            WHEN DATE_FORMAT(`o`.`created_at`, '%m') = '12' THEN 'Décembre'
        END,
        DATE_FORMAT(`o`.`created_at`, ' %Y à %H:%i')
    ) AS `date_display`,
    -- Added payer_name and payer_type columns
    CASE
        WHEN COALESCE(`main_oiable`.`account_type`, `oiable`.`account_type`) = 'congress' THEN 'Congrès'
        WHEN COALESCE(`main_oiable`.`account_type`, `oiable`.`account_type`) = 'group' THEN `g`.`name`
        WHEN COALESCE(`main_oiable`.`account_type`, `oiable`.`account_type`) = 'contact' THEN CONCAT(`u`.`first_name`, ' ', `u`.`last_name`)
        ELSE NULL
    END AS `payer_name`,
    COALESCE(`main_oiable`.`account_type`, `oiable`.`account_type`) AS `payer_type`
FROM
    `orders` `o`
    LEFT JOIN `order_invoices` `oi` ON `oi`.`order_id` = `o`.`id`
    LEFT JOIN `order_invoiceable` `oiable` ON `oiable`.`order_id` = `o`.`id`
    LEFT JOIN `orders` `parent` ON `parent`.`id` = `o`.`parent_id`
    LEFT JOIN `order_invoiceable` `main_oiable` ON `main_oiable`.`order_id` = `parent`.`id`
    LEFT JOIN `front_transactions` `ft` ON `ft`.`order_id` = `o`.`id`
    LEFT JOIN `groups` `g` ON `g`.`id` = COALESCE(`main_oiable`.`account_id`, `oiable`.`account_id`)
                           AND COALESCE(`main_oiable`.`account_type`, `oiable`.`account_type`) = 'group'
    LEFT JOIN `users` `u` ON `u`.`id` = COALESCE(`main_oiable`.`account_id`, `oiable`.`account_id`)
                          AND COALESCE(`main_oiable`.`account_type`, `oiable`.`account_type`) = 'contact'
    LEFT JOIN (
        SELECT `op`.`order_id`, SUM(`op`.`amount`) AS `payments_total`
        FROM `order_payments` `op`
        GROUP BY `op`.`order_id`
    ) `p` ON `p`.`order_id` = `o`.`id`
    LEFT JOIN (
        SELECT `ocs`.`order_id`, MAX(`ocs`.`cancellation_request`) AS `cancellation_request`,
               MAX(`ocs`.`cancelled_at`) AS `cancelled_at`
        FROM `order_cart_service` `ocs`
        GROUP BY `ocs`.`order_id`
    ) `ocs` ON `ocs`.`order_id` = `o`.`id`
    LEFT JOIN (
        SELECT `oca`.`order_id`, MAX(`oca`.`cancellation_request`) AS `cancellation_request`,
               MAX(`oca`.`cancelled_at`) AS `cancelled_at`
        FROM `order_cart_accommodation` `oca`
        GROUP BY `oca`.`order_id`
    ) `oca` ON `oca`.`order_id` = `o`.`id`
    LEFT JOIN `events_contacts` `ec` ON `o`.`client_type` IN ('contact', 'orator')
                                      AND `u`.`id` = `ec`.`user_id`
                                      AND `o`.`event_id` = `ec`.`event_id`
WHERE
    `o`.`parent_id` IS NULL OR `o`.`parent_id` IS NOT NULL
GROUP BY
    `o`.`id`;

        ",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW orders_view AS
            select `o`.`id` AS `id`,`o`.`uuid` AS `uuid`,`o`.`event_id` AS `event_id`,`o`.`created_at` AS `date`,`o`.`client_type` AS `client_type`,`o`.`origin` AS `origin`,`o`.`marker` AS `marker`,case when 'group' collate utf8mb4_unicode_ci = `o`.`client_type` then 'Groupe' when 'contact' collate utf8mb4_unicode_ci = `o`.`client_type` then 'Participant' when 'orator' collate utf8mb4_unicode_ci = `o`.`client_type` then 'Orateur' end AS `client_type_display`,`o`.`client_id` AS `client_id`,`o`.`status` AS `status`,`ft`.`transaction_id` AS `paybox_num_trans`,case when `o`.`client_type` = 'orator' then '-' when 'paid' collate utf8mb4_unicode_ci = `o`.`status` then 'Soldée' when 'unpaid' collate utf8mb4_unicode_ci = `o`.`status` then 'Non-soldée' end AS `status_display`,`oi`.`invoice_number` AS `invoice_number`,case when 'group' collate utf8mb4_unicode_ci = `o`.`client_type` then `g`.`name` when `o`.`client_type` collate utf8mb4_unicode_ci in ('contact','orator') then concat(`u`.`last_name`,' ',`u`.`first_name`) end AS `name`,format((`o`.`total_net` + `o`.`total_vat`) / 100,2) AS `total`,format(coalesce(`p`.`payments_total`,0) / 100,2) AS `payments_total`,format(`o`.`total_pec` / 100,2) AS `total_pec`,`ec`.`order_cancellation` AS `order_cancellation`,case when `o`.`cancellation_request` is not null then `o`.`cancellation_request` when `ocs`.`cancellation_request` is not null then `ocs`.`cancellation_request` when `oca`.`cancellation_request` is not null then `oca`.`cancellation_request` else NULL end AS `cancellation_request`,case when `o`.`cancelled_at` is not null then `o`.`cancelled_at` when `ocs`.`cancelled_at` is not null then `ocs`.`cancelled_at` when `oca`.`cancelled_at` is not null then `oca`.`cancelled_at` else NULL end AS `cancelled_at`,case when `oi`.`order_id` is not null then 1 else NULL end AS `has_invoice`,case when `o`.`client_type` = 'orator' then '-' when `oi`.`order_id` is not null then 'Oui' else 'Non' end AS `has_invoice_display`,case when exists(select 1 from `order_cart_service` `ocs1` where `ocs1`.`order_id` = `o`.`id` limit 1) and exists(select 1 from `order_cart_accommodation` `oca1` where `oca1`.`order_id` = `o`.`id` limit 1) then 'Prestations, Hébergement' when exists(select 1 from `order_cart_service` `ocs1` where `ocs1`.`order_id` = `o`.`id` limit 1) then 'Prestations' when exists(select 1 from `order_cart_accommodation` `oca1` where `oca1`.`order_id` = `o`.`id` limit 1) then 'Hébergement' else '-' end AS `contains`,case when `o`.`total_pec` > 0 then 'PEC' else NULL end AS `has_pec`,concat(date_format(`o`.`created_at`,'%d '),case when date_format(`o`.`created_at`,'%m') = '01' collate utf8mb4_unicode_ci then 'Janvier' when date_format(`o`.`created_at`,'%m') = '02' collate utf8mb4_unicode_ci then 'Février' when date_format(`o`.`created_at`,'%m') = '03' collate utf8mb4_unicode_ci then 'Mars' when date_format(`o`.`created_at`,'%m') = '04' collate utf8mb4_unicode_ci then 'Avril' when date_format(`o`.`created_at`,'%m') = '05' collate utf8mb4_unicode_ci then 'Mai' when date_format(`o`.`created_at`,'%m') = '06' collate utf8mb4_unicode_ci then 'Juin' when date_format(`o`.`created_at`,'%m') = '07' collate utf8mb4_unicode_ci then 'Juillet' when date_format(`o`.`created_at`,'%m') = '08' collate utf8mb4_unicode_ci then 'Août' when date_format(`o`.`created_at`,'%m') = '09' collate utf8mb4_unicode_ci then 'Septembre' when date_format(`o`.`created_at`,'%m') = '10' collate utf8mb4_unicode_ci then 'Octobre' when date_format(`o`.`created_at`,'%m') = '11' collate utf8mb4_unicode_ci then 'Novembre' when date_format(`o`.`created_at`,'%m') = '12' collate utf8mb4_unicode_ci then 'Décembre' end,date_format(`o`.`created_at`,' %Y à %H:%i')) AS `date_display` from ((((((((`orders` `o` left join `order_invoices` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `front_transactions` `ft` on(`ft`.`order_id` = `o`.`id`)) left join `groups` `g` on(`o`.`client_type` = 'group' and `g`.`id` = `o`.`client_id`)) left join `users` `u` on(`o`.`client_type` in ('contact','orator') and `u`.`id` = `o`.`client_id`)) left join (select `op`.`order_id` AS `order_id`,sum(`op`.`amount`) AS `payments_total` from `order_payments` `op` group by `op`.`order_id`) `p` on(`p`.`order_id` = `o`.`id`)) left join `events_contacts` `ec` on(`o`.`client_type` in ('contact','orator') and `u`.`id` = `ec`.`user_id` and `o`.`event_id` = `ec`.`event_id`)) left join (select `ocs`.`order_id` AS `order_id`,max(`ocs`.`cancellation_request`) AS `cancellation_request`,max(`ocs`.`cancelled_at`) AS `cancelled_at` from `order_cart_service` `ocs` group by `ocs`.`order_id`) `ocs` on(`ocs`.`order_id` = `o`.`id`)) left join (select `oca`.`order_id` AS `order_id`,max(`oca`.`cancellation_request`) AS `cancellation_request`,max(`oca`.`cancelled_at`) AS `cancelled_at` from `order_cart_accommodation` `oca` group by `oca`.`order_id`) `oca` on(`oca`.`order_id` = `o`.`id`)) where `o`.`parent_id` is null group by `o`.`id`
        ",
        );
    }
};
