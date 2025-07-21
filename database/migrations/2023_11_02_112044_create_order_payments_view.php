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
        DB::statement("
CREATE OR REPLACE VIEW order_payments_view AS
SELECT
    op.id AS payment_id,
    o.id AS order_id,
    oi.invoice_number,
    CONCAT(p.first_name, ' ', p.last_name) AS payer_name, 
    op.date,
    op.payment_method,
    op.authorization_number,
    op.card_number,
    op.bank,
    op.issuer,
    op.check_number,
    op.price
FROM order_payments AS op
INNER JOIN order_invoices AS oi ON oi.id = op.invoice_id        
INNER JOIN orders AS o ON o.id = oi.order_id
LEFT JOIN order_payer AS p ON p.id = o.payer_id
        
;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS order_payments_view");
    }
};
