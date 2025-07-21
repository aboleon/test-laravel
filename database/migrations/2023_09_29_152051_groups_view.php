<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW group_view AS
        select a.id, a.name, a.company,
JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
FROM `groups` a
    LEFT JOIN group_address b on a.id = b.group_id
LEFT JOIN countries c ON b.country_code = c.code");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS group_view");
    }
};
