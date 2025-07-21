<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW place_view AS
        select 
            p.id, 
            p.name, 
            p.email, 
            p.phone, 
            pa.locality, 
            JSON_UNQUOTE(JSON_EXTRACT(dpt.name, '$.fr')) AS type,
            JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
        FROM places p
        LEFT JOIN dictionnary_entries dpt ON dpt.id = p.place_type_id
        LEFT JOIN place_addresses pa ON p.id = pa.place_id
        LEFT JOIN countries c ON pa.country_code = c.code"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS place_view");
    }
};
