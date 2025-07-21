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
        DB::statement("CREATE OR REPLACE VIEW `order_refunds_view` AS
        SELECT
    b.id AS id,
    b.refund_number AS refund_number,
    b.uuid AS uuid,
    DATE_FORMAT(b.created_at, '%d/%m/%Y %H:%i') AS created_at,
    b.created_at as created_at_raw,
    b.order_id AS order_id,
    a.event_id AS event_id,
    CONCAT(c.first_name, ' ', c.last_name) AS client_name,
    c.account_id AS client_id,
    c.account_type AS client_type,
    FORMAT(SUM(d.amount) / 100, 2) AS total,
    SUM(d.amount) AS total_raw,
    (e.rate / 100) AS vat_rate,
    e.rate AS vat_rate_raw
FROM order_refunds b
JOIN orders a ON a.id = b.order_id
JOIN order_invoiceable c ON a.id = c.order_id
JOIN order_refunds_items d ON b.id = d.refund_id
JOIN vat e ON e.id = d.vat_id
GROUP BY b.id, b.refund_number, b.uuid, b.created_at, b.order_id, a.event_id, c.first_name, c.last_name
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `order_refunds_view`");
    }
};
