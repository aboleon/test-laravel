<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS account_view");
        DB::statement("CREATE VIEW account_view AS
SELECT a.id, a.first_name, a.last_name, a.email, a.deleted_at,
f.phone,
b.blacklisted, b.notes, b.account_type,
JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS domain,
t.company,
t.locality,
JSON_UNQUOTE(JSON_EXTRACT(e.name, '$.fr')) AS country
FROM users a
JOIN account_profile b ON a.id = b.user_id
LEFT JOIN dictionnary_entries c ON c.id = b.domain_id
LEFT JOIN (
    SELECT user_id, company, locality, country_code
    FROM (
        SELECT user_id, company, locality, country_code,
               CASE
                   WHEN company IS NOT NULL THEN 1
                   WHEN billing = 1 THEN 2
                   ELSE 3
               END AS priority
        FROM account_address
        ORDER BY priority, id
    ) AS sub
    GROUP BY user_id, company, locality, country_code
) AS t ON a.id = t.user_id
LEFT JOIN (
    SELECT user_id, phone
    FROM (
        SELECT user_id, phone,
               CASE
                   WHEN `default` IS NOT NULL THEN 1 ELSE 2
               END AS priority
        FROM account_phones
        ORDER BY priority, id
    ) AS sub
    GROUP BY user_id, phone
) AS f ON a.id = f.user_id
LEFT JOIN countries e ON t.country_code = e.code;
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS account_view");
    }
};
