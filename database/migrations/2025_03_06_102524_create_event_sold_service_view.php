<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE VIEW event_sellables_orders AS
        SELECT
    ocs.order_id,
    o.event_id,
    ocs.service_id,
	 ocs.quantity,
    ocs.cancelled_at AS cart_canceled,
    o.cancelled_at AS order_cancelled_at,
    o.client_type,
    CASE
        WHEN o.client_type = 'group' THEN g.name
        ELSE CONCAT_WS(' ', u.first_name, u.last_name)
    END AS name,
    ec.id AS event_contact_id,
    CASE
        WHEN ocs.cancelled_at IS NOT NULL OR o.cancelled_at IS NOT NULL THEN 1
        ELSE 0
    END AS cancelled,
    COALESCE(ocs.cancelled_at, o.cancelled_at) AS cancelled_date,
    CASE
        WHEN ocs.cancelled_at IS NOT NULL OR o.cancelled_at IS NOT NULL THEN 'Annulation'
        ELSE 'Ok'
    END AS status
FROM order_cart_service ocs
JOIN orders o ON o.id = ocs.order_id
LEFT JOIN users u ON u.id = o.client_id AND o.client_type != 'group'
LEFT JOIN groups g ON g.id = o.client_id AND o.client_type = 'group'
LEFT JOIN events_contacts ec ON ec.user_id = u.id AND ec.event_id = o.event_id
",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW event_sellables_orders");
    }
};
