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
CREATE OR REPLACE VIEW order_invoices_cancels_view AS

            SELECT 
                c.id,
                o.id AS order_id,
                i.invoice_number,
                CONCAT(p.first_name, ' ', p.last_name) AS payer,
                c.date,
                c.price_after_tax
                
            FROM order_invoices_cancels c
            INNER JOIN order_invoices i ON i.id = c.invoice_id
            INNER JOIN orders o ON o.id = i.order_id
            LEFT JOIN order_payer p ON o.payer_id = p.id
;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS order_invoices_cancels_view");
    }
};
