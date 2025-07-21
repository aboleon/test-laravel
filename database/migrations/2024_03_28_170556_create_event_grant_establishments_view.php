<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW event_grant_establishments_view AS
        SELECT
            eg.grant_id,
            eg.pax,
            e.*,
            JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
        FROM
            event_grant_establishments eg
            INNER JOIN establishments e ON e.id = eg.establishment_id
            INNER JOIN countries c ON c.code = e.country_code
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_grant_establishments_view");
    }
};
