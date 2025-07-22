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
        DB::statement("CREATE OR REPLACE VIEW order_payments_view AS
SELECT
    `b`.`id` AS `id`,
    `d`.`invoice_number` AS `invoice_number`,
    `d`.`id` AS `invoice_id`,
    `a`.`id` AS `order_id`,
    `a`.`event_id` AS `event_id`,
    `a`.`uuid` AS `uuid`,
    CONCAT(`c`.`first_name`, ' ', `c`.`last_name`) AS `payer`,
    FORMAT(`b`.`amount` / 100, 2) AS `amount`,
    DATE_FORMAT(`b`.`date`, '%d/%m/%Y') AS `date_formatted`,
    DATE_FORMAT(`b`.`date`, '%Y-%m-%d') AS `date`,
    `b`.`authorization_number` AS `authorization_number`,
    `b`.`payment_method` AS `payment_method`,
    CASE
        WHEN `b`.`payment_method` = 'cb_paybox' THEN 'CB (Paybox)'
        WHEN `b`.`payment_method` = 'cb_vad' THEN 'CB (VAD)'
        WHEN `b`.`payment_method` = 'check' THEN 'Chèque'
        WHEN `b`.`payment_method` = 'bank_transfer' THEN 'Virement'
        WHEN `b`.`payment_method` = 'cash' THEN 'Espèces'
        ELSE 'Non spécifié'
    END AS `payment_method_translated`,
    `b`.`bank` AS `bank`,
    `b`.`issuer` AS `issuer`,
    `b`.`check_number` AS `check_number`,
    `b`.`card_number` AS `card_number`
FROM (((`order_payments` `b`
    JOIN `orders` `a` ON `a`.`id` = `b`.`order_id`)
    JOIN `order_invoiceable` `c` ON `a`.`id` = `c`.`order_id`)
    LEFT JOIN `order_invoices` `d` ON `d`.`order_id` = `a`.`id`)
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW order_payments_view AS
select `b`.`id` AS `id`,`d`.`invoice_number` AS `invoice_number`,`d`.`id` AS `invoice_id`,`a`.`id` AS `order_id`,`a`.`event_id` AS `event_id`,`a`.`uuid` AS `uuid`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `payer`,format(`b`.`amount` / 100,2) AS `amount`,date_format(`b`.`date`,'%d/%m/%Y') AS `date`,`b`.`authorization_number` AS `authorization_number`,`b`.`payment_method` AS `payment_method`,case when `b`.`payment_method` = 'cb_paybox' then 'CB (Paybox)' when `b`.`payment_method` = 'cb_vad' then 'CB (VAD)' when `b`.`payment_method` = 'check' then 'Chèque' when `b`.`payment_method` = 'bank_transfer' then 'Virement' when `b`.`payment_method` = 'cash' then 'Espèces' else 'Non spécifié' end AS `payment_method_translated`,`b`.`bank` AS `bank`,`b`.`issuer` AS `issuer`,`b`.`check_number` AS `check_number`,`b`.`card_number` AS `card_number` from (((`order_payments` `b` join `orders` `a` on(`a`.`id` = `b`.`order_id`)) join `order_invoiceable` `c` on(`a`.`id` = `c`.`order_id`)) left join `order_invoices` `d` on(`d`.`order_id` = `a`.`id`))
        ;");
    }
};
