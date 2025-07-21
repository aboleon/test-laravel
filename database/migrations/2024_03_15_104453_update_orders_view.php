<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS orders_view");
        DB::statement("
CREATE VIEW orders_view AS
SELECT 
    o.id,
    o.event_id,
    o.created_at as date,
    o.client_type,
    CASE 
        WHEN 'group' = o.client_type THEN 'Groupe'
        WHEN 'contact' = o.client_type THEN 'Participant'
    END as client_type_display,
    o.client_id,
    o.status,
    CASE 
        WHEN 'paid' = o.status THEN 'Soldée'
        WHEN 'unpaid' = o.status THEN 'Non-soldée'
    END as status_display,
    oi.invoice_number,
    CASE 
    	WHEN 'group' = o.client_type THEN g.name
    	WHEN 'contact' = o.client_type THEN CONCAT(u.last_name, ' ', u.first_name) 
    END as name,
    o.total_net + o.total_vat as total,
    COALESCE(p.payments_total, 0) as payments_total,
    ec.order_cancellation,
    CASE 
        WHEN oi.order_id IS NOT NULL THEN 1
        ELSE NULL
    END as has_invoice,
    CASE 
        WHEN oi.order_id IS NOT NULL THEN 'Oui'
        ELSE 'Non'
    END as has_invoice_display
    
    
FROM orders o
LEFT JOIN order_invoices oi ON oi.order_id = o.id 
LEFT JOIN `groups` g ON o.client_type = 'group' AND g.id = o.client_id 
LEFT JOIN users u ON o.client_type = 'contact' AND u.id = o.client_id 
LEFT JOIN (
	SELECT 
	order_id,
	SUM(amount) as payments_total	
	FROM order_payments op 
	GROUP BY order_id
) p on p.order_id = o.id
LEFT JOIN events_contacts ec ON o.client_type = 'contact' AND u.id = ec.user_id AND o.event_id = ec.event_id
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS orders_view");
    }
};
