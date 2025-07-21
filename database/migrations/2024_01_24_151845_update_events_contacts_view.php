<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_view");
        DB::statement("
CREATE VIEW event_contact_view AS
SELECT 
    ec.id,
    e.id as event_id,
    u.id AS user_id,
    u.first_name,
    u.last_name,
    u.email,
    JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) AS domain,
    CASE
        WHEN ap.account_type = 'company' THEN 'Sociétés'
        WHEN ap.account_type = 'medical' THEN 'Professionnels de santé'
        WHEN ap.account_type = 'other' THEN 'Autres'
    END AS account_type_display,    
    ap.company_name,
    a.locality,
    JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country,
    JSON_UNQUOTE(JSON_EXTRACT(de.name, '$.fr')) AS fonction,
    GROUP_CONCAT(g.name SEPARATOR ', ') AS `group`,
    CONCAT(',', GROUP_CONCAT(g.id SEPARATOR ','), ',') AS `group_ids`,
    ec.created_at,
    ec.participation_type_group,
    CASE
        WHEN ec.participation_type_group IS NULL THEN '-'
        WHEN ec.participation_type_group = 'congress' THEN 'Congressistes'
        WHEN ec.participation_type_group = 'orator' THEN 'Orateurs'
        WHEN ec.participation_type_group = 'industry' THEN 'Industriels'
    END AS participation_type_group_display,
    JSON_UNQUOTE(JSON_EXTRACT(pt.name, '$.fr')) AS participation_type
FROM events_contacts ec
INNER JOIN users u ON u.id = ec.user_id
INNER JOIN events e ON e.id = ec.event_id
INNER JOIN account_profile ap ON u.id = ap.user_id
LEFT JOIN participation_types pt ON pt.id = ec.participation_type_id
LEFT JOIN dictionnary_entries d ON ap.domain_id = d.id
LEFT JOIN dictionnary_entries de ON ap.profession_id = de.id
LEFT JOIN main_account_address_view a ON u.id = a.user_id
LEFT JOIN countries c ON a.country_code = c.code
LEFT JOIN group_contacts gc ON u.id = gc.user_id
LEFT JOIN `groups` g ON gc.group_id = g.id
GROUP BY ec.id, event_id, u.id, u.first_name, u.last_name, u.email, domain, account_type_display, ap.company_name, a.locality, country, fonction, ec.created_at, participation_type_group, participation_type_group_display, participation_type;
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_view");
    }
};
