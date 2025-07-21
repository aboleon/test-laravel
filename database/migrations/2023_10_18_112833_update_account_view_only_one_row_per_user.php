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
SELECT
    a.id,
    a.first_name,
    a.last_name,
    a.email,
    a.deleted_at,
    ph.phone,
    b.blacklisted,
    b.notes,
    b.account_type,
    JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS domain,
    addr.company,
    addr.locality,
    JSON_UNQUOTE(JSON_EXTRACT(cn.name, '$.fr')) AS country
FROM
    users a
        JOIN account_profile b ON a.id = b.user_id
        LEFT JOIN dictionnary_entries c ON c.id = b.domain_id
        LEFT JOIN main_account_address_view addr ON a.id = addr.user_id
        LEFT JOIN main_account_phone_view ph ON a.id = ph.user_id
        LEFT JOIN countries cn ON addr.country_code = cn.code;



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
