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
        DB::statement("
      CREATE OR REPLACE VIEW event_program_interventions_view AS
SELECT
    i.id,
    dr.event_id,
    i.event_program_session_id,
    CONCAT(DATE_FORMAT(dr.datetime_start, '%d/%m/%Y %Hh%i'), ' - ',  pmain.name, ' > ', JSON_UNQUOTE(JSON_EXTRACT(rmain.name, '$.fr'))) AS container,
    DATE_FORMAT(i.start, '%H:%i') AS start,
    DATE_FORMAT(i.end, '%H:%i') AS end,
    JSON_UNQUOTE(JSON_EXTRACT(s.name, '$.fr')) as session,
    JSON_UNQUOTE(JSON_EXTRACT(i.name, '$.fr')) as name,

    GROUP_CONCAT(CONCAT(u.last_name, ' ', u.first_name) ORDER BY u.last_name ASC SEPARATOR ', ') AS orators,

    JSON_UNQUOTE(JSON_EXTRACT(dictionnary_entries.name, '$.fr')) as specificity,
    i.duration,
    CASE WHEN i.is_online = 1 THEN 'Oui' ELSE 'Non' END AS is_online

    FROM event_program_interventions AS i
    JOIN event_program_sessions AS s ON i.event_program_session_id = s.id
    JOIN event_program_day_rooms dr ON s.event_program_day_room_id = dr.id
    JOIN place_rooms AS rmain ON dr.room_id = rmain.id
    JOIN places AS pmain ON rmain.place_id = pmain.id
    LEFT JOIN dictionnary_entries ON i.specificity_id = dictionnary_entries.id
    LEFT JOIN event_program_intervention_orators AS eci ON eci.event_program_intervention_id = i.id
    LEFT JOIN events_contacts AS c ON c.id = eci.events_contacts_id
    LEFT JOIN users AS u ON u.id = c.user_id
    GROUP BY i.id, dr.datetime_start, i.start, i.end, s.name, i.name, dictionnary_entries.name;

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_program_interventions_view");
    }
};
