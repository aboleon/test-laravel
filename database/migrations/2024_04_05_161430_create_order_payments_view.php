<?php

use App\Enum\PaymentMethod;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $query = "CREATE OR REPLACE VIEW order_payments_view AS
            SELECT
                    b.id,
                    d.invoice_number,
                    d.id as invoice_id,
                    a.id AS order_id,
                    a.event_id,
                    a.uuid,
                    CONCAT(c.first_name, ' ', c.last_name) AS payer,
                    FORMAT(b.amount/100, 2) AS amount,
                    DATE_FORMAT(b.`date`, '%d/%m/%Y') AS date,
                    b.authorization_number,
                    b.payment_method,
                    CASE " . "\n";

        foreach (PaymentMethod::values() as $paymentMethod) {
            $query .= "WHEN b.payment_method='" . $paymentMethod . "'  THEN '" . PaymentMethod::translated($paymentMethod) . "' " . "\n";
        }
        $query .= "ELSE 'Non spécifié'
                    END AS payment_method_translated,
                    b.bank,
                    b.issuer,
                    b.check_number,
                    b.card_number
                FROM order_payments b
                JOIN orders a ON a.id = b.order_id
                JOIN order_invoiceable c ON a.id = c.order_id
				LEFT JOIN order_invoices d ON d.order_id = a.id;";

        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW order_payments_view');
    }
};
