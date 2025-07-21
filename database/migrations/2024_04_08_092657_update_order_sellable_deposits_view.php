<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("CREATE OR REPLACE VIEW order_sellable_deposits_view AS
        SELECT
            osd.id,
            osd.order_id,
            osd.event_id,
            osd.shoppable_label,
            osd.total_net,
            osd.event_contact_id,
            osd.status,
            osd.total_net + osd.total_vat AS total_ttc,
            DATE_FORMAT(osd.created_at, '%d/%m/%Y %H:%i:%s') as date_fr,
            CONCAT(u.first_name, ' ', u.last_name) as beneficiary_name

        FROM order_sellable_deposits osd
        LEFT JOIN orders o ON osd.order_id = o.id
        LEFT JOIN events_contacts ec ON osd.event_contact_id = ec.id
        LEFT JOIN users u ON ec.user_id = u.id





");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW order_sellable_deposits_view");
    }
};
