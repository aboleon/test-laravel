<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW event_deposits_view AS
        select `ed`.`id` AS `id`,`ed`.`order_id` AS `order_id`,`o`.`uuid` AS `uuid`,`ed`.`event_id` AS `event_id`,`ed`.`shoppable_type` AS `shoppable_type`,`ed`.`shoppable_label` AS `shoppable_label`,`ed`.`total_net` AS `total_net`,`ed`.`event_contact_id` AS `event_contact_id`,`ed`.`status` AS `status`,`ed`.`reimbursed_at` AS `reimbursed_at`,case when `oi`.`order_id` is not null then 1 else NULL end AS `has_invoice`,case when `e`.`ends` < curdate() and `ec`.`is_attending` is null then 1 else 0 end AS `is_attending_expired`,`ed`.`total_net` + `ed`.`total_vat` AS `total_ttc`,date_format(`ed`.`created_at`,'%d/%m/%Y %H:%i:%s') AS `date_fr`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `beneficiary_name`,`pc`.`id` AS `payment_call_id` from ((((((`event_deposits` `ed` left join `orders` `o` on(`ed`.`order_id` = `o`.`id`)) left join `order_invoices` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `events_contacts` `ec` on(`ed`.`event_contact_id` = `ec`.`id`)) left join `users` `u` on(`ec`.`user_id` = `u`.`id`)) left join `events` `e` on(`ed`.`event_id` = `e`.`id`)) left join `payment_call` `pc` on(`pc`.`shoppable_type` = 'App\\\\Models\\\\Order\\\\EventDeposit' and `pc`.`shoppable_id` = `ed`.`id`)) where `ed`.`status`!='temp'
        ",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW event_deposits_view AS
        select `ed`.`id` AS `id`,`ed`.`order_id` AS `order_id`,`o`.`uuid` AS `uuid`,`ed`.`event_id` AS `event_id`,`ed`.`shoppable_type` AS `shoppable_type`,`ed`.`shoppable_label` AS `shoppable_label`,`ed`.`total_net` AS `total_net`,`ed`.`event_contact_id` AS `event_contact_id`,`ed`.`status` AS `status`,`ed`.`reimbursed_at` AS `reimbursed_at`,case when `oi`.`order_id` is not null then 1 else NULL end AS `has_invoice`,case when `e`.`ends` < curdate() and `ec`.`is_attending` is null then 1 else 0 end AS `is_attending_expired`,`ed`.`total_net` + `ed`.`total_vat` AS `total_ttc`,date_format(`ed`.`created_at`,'%d/%m/%Y %H:%i:%s') AS `date_fr`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `beneficiary_name`,`pc`.`id` AS `payment_call_id` from ((((((`event_deposits` `ed` left join `orders` `o` on(`ed`.`order_id` = `o`.`id`)) left join `order_invoices` `oi` on(`oi`.`order_id` = `o`.`id`)) left join `events_contacts` `ec` on(`ed`.`event_contact_id` = `ec`.`id`)) left join `users` `u` on(`ec`.`user_id` = `u`.`id`)) left join `events` `e` on(`ed`.`event_id` = `e`.`id`)) left join `payment_call` `pc` on(`pc`.`shoppable_type` = 'App\\\\Models\\\\Order\\\\EventDeposit' and `pc`.`shoppable_id` = `ed`.`id`))
        ",
        );
    }
};
