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
    dr.event_id,
    JSON_UNQUOTE(JSON_EXTRACT(s.name, '$.fr')) AS name,
    DATE_FORMAT(dr.datetime_start, '%d/%m/%Y') as datetime_start,
    CONCAT(p.name, ' > ', JSON_UNQUOTE(JSON_EXTRACT(pr.name, '$.fr'))) as place_room,
    GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) ORDER BY u.first_name ASC SEPARATOR ', ') AS moderators

FROM event_program_sessions s
INNER JOIN event_program_day_rooms dr ON s.event_program_day_room_id = dr.id
LEFT JOIN place_rooms pr ON pr.id = s.place_room_id
LEFT JOIN places p ON p.id = pr.place_id
LEFT JOIN event_program_session_moderators AS m ON m.event_program_session_id = s.id
LEFT JOIN events_contacts AS c ON c.id = m.events_contacts_id
LEFT JOIN users AS u ON u.id = c.user_id

GROUP BY s.id, dr.event_id, s.name, dr.datetime_start, pr.name, p.name

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
