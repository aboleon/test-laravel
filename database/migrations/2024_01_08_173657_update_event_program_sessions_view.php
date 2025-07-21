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
            CONCAT(p.name, ' > ', JSON_UNQUOTE(JSON_EXTRACT(pr.name, '$.fr'))) as place_room,
            COALESCE(m.moderators, '') as moderators
        
        FROM event_program_sessions s
        INNER JOIN event_program_day_rooms dr ON s.event_program_day_room_id = dr.id
        LEFT JOIN place_rooms pr ON pr.id = s.place_room_id
        LEFT JOIN places p ON p.id = pr.place_id
        LEFT JOIN (
            SELECT 
                epi.event_program_session_id, 
                GROUP_CONCAT(DISTINCT u.first_name, ' ', u.last_name ORDER BY u.first_name, u.last_name SEPARATOR ', ') as moderators
            FROM event_program_interventions epi
            INNER JOIN events_contacts_moderators_interventions ecmi ON ecmi.event_program_intervention_id = epi.id
            INNER JOIN events_contacts ec ON ec.id = ecmi.events_contacts_id
            INNER JOIN users u ON u.id = ec.user_id
            GROUP BY epi.event_program_session_id
        ) m ON m.event_program_session_id = s.id


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
