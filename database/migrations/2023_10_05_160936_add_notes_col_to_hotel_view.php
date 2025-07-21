<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS hotel_view");
        DB::statement("CREATE VIEW hotel_view AS 
        select a.id, a.name, a.email, a.phone, b.locality,
        JSON_UNQUOTE(JSON_EXTRACT(a.description, '$.fr')) AS description,
        JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
        FROM hotels a
        LEFT JOIN hotel_address b ON a.id = b.hotel_id
        LEFT JOIN countries c ON b.country_code = c.code");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS hotel_view");
    }
};
