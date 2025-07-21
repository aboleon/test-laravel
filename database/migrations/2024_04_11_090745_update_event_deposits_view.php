<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_deposits_view AS
        SELECT
            ed.id,
            ed.order_id,
            ed.event_id,
            ed.shoppable_label,
            ed.total_net,
            ed.event_contact_id,
            ed.status,
            ed.reimbursed_at,
            CASE
                WHEN e.ends < CURDATE() AND ec.is_attending IS NULL THEN 1
                ELSE 0
            END AS is_attending_expired,
            ed.total_net + ed.total_vat AS total_ttc,
            DATE_FORMAT(ed.created_at, '%d/%m/%Y %H:%i:%s') as date_fr,
            CONCAT(u.first_name, ' ', u.last_name) as beneficiary_name

        FROM event_deposits ed
        LEFT JOIN orders o ON ed.order_id = o.id
        LEFT JOIN events_contacts ec ON ed.event_contact_id = ec.id
        LEFT JOIN users u ON ec.user_id = u.id
        LEFT JOIN events e ON ed.event_id = e.id





");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW event_deposits_view");
    }
};
