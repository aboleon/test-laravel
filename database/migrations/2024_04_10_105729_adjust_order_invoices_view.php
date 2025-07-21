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
        DB::statement("CREATE OR REPLACE VIEW order_invoices_view AS
        select `b`.`id`                                                               AS `id`,
       `b`.`invoice_number`,
       DATE_FORMAT(`b`.`created_at`, '%d/%m/%Y %H:%i')                        AS `created_at`,
       `a`.`id`                                                               AS `order_id`,
       `a`.`event_id`,
       `a`.`uuid`,
       concat(`c`.`first_name`, ' ', `c`.`last_name`)                         AS `client_name`,
       format((`a`.`total_vat` + `a`.`total_net`) / 100, 2)                   AS `total`,
       format((`a`.`total_vat` + `a`.`total_net` - `a`.`total_pec`) / 100, 2) AS `total_paid`
from ((`order_invoices` `b` join `orders` `a` on (`a`.`id` = `b`.`order_id`)) join `order_invoiceable` `c`
      on (`a`.`id` = `c`.`order_id`))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE OR REPLACE VIEW order_invoices_view AS
        select `b`.`id` AS `id`,`b`.`invoice_number` AS `invoice_number`,`b`.`created_at` AS `created_at`,`a`.`id` AS `order_id`,`a`.`event_id` AS `event_id`,`a`.`uuid` AS `uuid`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `client_name`,format(((`a`.`total_vat` + `a`.`total_net`) / 100),2) AS `total`,format((((`a`.`total_vat` + `a`.`total_net`) - `a`.`total_pec`) / 100),2) AS `total_paid`,`b`.`paid_at` AS `paid_at`,(case when (`b`.`paid_at` is null) then 'Non payée' else 'Payée' end) AS `paid_status` from ((`order_invoices` `b` join `orders` `a` on((`a`.`id` = `b`.`order_id`))) join `order_invoiceable` `c` on((`a`.`id` = `c`.`order_id`)))
        ");


    }
};
