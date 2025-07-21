<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("CREATE OR REPLACE VIEW event_program_sessions_view AS
        SELECT
            s.id,
            JSON_UNQUOTE(JSON_EXTRACT(s.name, '$.fr')) AS name,
            DATE_FORMAT(dr.datetime_start, '%d/%m/%Y') as datetime_start,
            CONCAT(p.name, ' > ', JSON_UNQUOTE(JSON_EXTRACT(pr.name, '$.fr'))) as place_room

        FROM event_program_sessions s
        INNER JOIN event_program_day_rooms dr ON s.event_program_day_room_id = dr.id
        LEFT JOIN place_rooms pr ON pr.id = s.place_room_id
        LEFT JOIN places p ON p.id = pr.place_id
");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_program_sessions_view");
    }
};
