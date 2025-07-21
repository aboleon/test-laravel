<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW place_view as
        select a.id, a.name, a.email, a.phone, b.locality,
JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
FROM places a
LEFT JOIN place_addresses b ON a.id = b.place_id
LEFT JOIN countries c ON b.country_code = c.code");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS place_view");
    }
};
