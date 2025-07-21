<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS place_room_view");
        DB::statement("CREATE VIEW place_room_view as
        select 
            id, 
            place_id,
            JSON_UNQUOTE(JSON_EXTRACT(name, '$.fr')) AS name,
            JSON_UNQUOTE(JSON_EXTRACT(level, '$.fr')) AS level
        FROM place_rooms"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS place_room_view");
    }
};
