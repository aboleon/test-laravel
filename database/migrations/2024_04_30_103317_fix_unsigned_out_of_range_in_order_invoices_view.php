<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW order_invoices_view AS
            SELECT
                b.id AS id,
                b.invoice_number AS invoice_number,
                b.created_at AS created_at,
                a.id AS order_id,
                a.event_id AS event_id,
                a.uuid AS uuid,
                CONCAT(c.first_name, ' ', c.last_name) AS client_name,
                FORMAT(((CAST(a.total_vat AS SIGNED) + CAST(a.total_net AS SIGNED)) / 100), 2) AS total,
                FORMAT(((CAST(a.total_vat AS SIGNED) + CAST(a.total_net AS SIGNED) - CAST(a.total_pec AS SIGNED)) / 100), 2) AS total_paid,
                b.paid_at AS paid_at,
                CASE
                    WHEN b.paid_at IS NULL THEN 'Non payée'
                    ELSE 'Payée'
                END AS paid_status
            FROM
                order_invoices b
            JOIN
                orders a ON (a.id = b.order_id)
            JOIN
                order_invoiceable c ON (a.id = c.order_id)
            ;"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW order_invoices_view AS select `b`.`id` AS `id`,`b`.`invoice_number` AS `invoice_number`,date_format(`b`.`created_at`,'%d/%m/%Y %H:%i') AS `created_at`,`a`.`id` AS `order_id`,`a`.`event_id` AS `event_id`,`a`.`uuid` AS `uuid`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `client_name`,format((`a`.`total_vat` + `a`.`total_net`) / 100,2) AS `total`,format((`a`.`total_vat` + `a`.`total_net` - `a`.`total_pec`) / 100,2) AS `total_paid` from ((`order_invoices` `b` join `orders` `a` on(`a`.`id` = `b`.`order_id`)) join `order_invoiceable` `c` on(`a`.`id` = `c`.`order_id`));"
        );
    }
};
