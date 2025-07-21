<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_group_contact_view");
        DB::statement("
CREATE VIEW event_group_contact_view AS
SELECT 
    egc.id,
    u.id AS user_id,
    u.first_name,
    u.last_name,
    u.email,
    a.locality,
    JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country,
    CASE 
        WHEN u.id = eg.main_contact_id
        THEN true 
        ELSE false 
        END
    as is_main_contact
    
FROM event_group_contacts egc
INNER JOIN event_groups eg ON eg.id = egc.event_group_id
INNER JOIN users u ON u.id = egc.user_id
INNER JOIN account_profile ap ON u.id = ap.user_id
LEFT JOIN main_account_address_view a ON u.id = a.user_id
LEFT JOIN countries c ON a.country_code = c.code
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_group_contact_view");
    }
};
