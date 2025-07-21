<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_dashboard_choosable_view");
        DB::statement("CREATE VIEW event_contact_dashboard_choosable_view AS

SELECT 
    c.id,
    ec.id AS event_contact_id,
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(s.title, '$.fr'))) AS title,
    DATE_FORMAT(s.service_date, '%d/%m/%Y') AS date,
    CASE c.status
        WHEN 'pending' THEN 'En attente'
        WHEN 'validated' THEN 'Validé'
        WHEN 'denied' THEN 'Refusé'
    END AS status
FROM event_contact_sellable_service_choosables c 
INNER JOIN event_sellable_service s ON s.id = c.choosable_id
INNER JOIN events_contacts ec ON ec.id = c.event_contact_id;

;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_dashboard_choosable_view");
    }
};
