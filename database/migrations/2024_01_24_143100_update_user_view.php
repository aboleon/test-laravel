<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_view");
        DB::statement("
CREATE VIEW user_view AS
SELECT 
    u.id,
    u.first_name,
    u.last_name,
    u.email,
    u.deleted_at,
    JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) AS domain,
    ap.account_type AS participation,
    ap.company_name,
    a.locality,
    JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country,
    JSON_UNQUOTE(JSON_EXTRACT(de.name, '$.fr')) AS fonction,
    GROUP_CONCAT(g.name SEPARATOR ', ') AS `group`,
    CONCAT(',', GROUP_CONCAT(g.id SEPARATOR ','), ',') AS `group_ids`
    
FROM users u 
INNER JOIN account_profile ap ON u.id = ap.user_id
LEFT JOIN dictionnary_entries d ON ap.domain_id = d.id
LEFT JOIN dictionnary_entries de ON ap.profession_id = de.id
LEFT JOIN main_account_address_view a ON u.id = a.user_id
LEFT JOIN countries c ON a.country_code = c.code
LEFT JOIN group_contacts gc ON u.id = gc.user_id
LEFT JOIN `groups` g ON gc.group_id = g.id
GROUP BY u.id, u.first_name, u.last_name, u.email, domain, participation, ap.company_name, a.locality, country, fonction;
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS user_view");
    }
};
