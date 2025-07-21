<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW order_invoices_view AS
            SELECT
                    b.id,
                    b.invoice_number,
                    b.created_at,
                    a.id AS order_id,
                    a.event_id,
                    a.uuid,
                    CONCAT(c.first_name, ' ', c.last_name) AS client_name,
                    FORMAT((a.total_vat + a.total_net)/100, 2) AS total,
                    FORMAT((a.total_vat + a.total_net - a.total_pec)/100, 2) as total_paid,
                    b.paid_at,
                    CASE
                        WHEN b.paid_at IS NULL THEN 'Non payée'
                        ELSE 'Payée'
                    END AS paid_status
                FROM order_invoices b
                JOIN orders a ON a.id = b.order_id
                JOIN order_invoiceable c ON a.id = c.order_id;"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW order_invoices_view AS SELECT
                    b.id,
                    b.invoice_number,
                    b.created_at,
                    a.id AS order_id,
                    a.event_id,
                    CONCAT(c.first_name, ' ', c.last_name) AS client_name,
                    (a.total_vat + a.total_net) AS total,
                    (a.total_vat + a.total_net - a.total_pec) as total_paid,
                    b.paid_at,
                    CASE
                        WHEN b.paid_at IS NULL THEN 'Non payée'
                        ELSE 'Payée'
                    END AS paid_status
                FROM order_invoices b
                JOIN orders a ON a.id = b.order_id
                JOIN order_invoiceable c ON a.id = c.order_id;"
        );
    }
};
