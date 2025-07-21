<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW establishment_view AS
        select a.id, a.name, a.locality, a.administrative_area_level_1 as region, a.administrative_area_level_2 as department,
JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
FROM establishments a
LEFT JOIN countries c ON a.country_code = c.code");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS establishment_view");
    }
};
