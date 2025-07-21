<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_dashboard_intervention_view");
        DB::statement("CREATE VIEW event_contact_dashboard_intervention_view AS
        
        SELECT ec.id                                        AS event_contact_id,
               epi.id                                       AS intervention_id,
               eps.id                                       AS session_id,
               epio.status                                  AS status,
               DATE_FORMAT(epi.start, '%d/%m/%Y')           AS date_fr,
               DATE_FORMAT(epi.start, '%H\\h%i')            AS start_time,
               DATE_FORMAT(epi.end, '%H\\h%i')              AS end_time,
               CASE
                   WHEN FLOOR(epi.duration / 60) > 0 THEN
                       CONCAT(LPAD(FLOOR(epi.duration / 60), 2, '0'), 'h', LPAD(epi.duration % 60, 2, '0'),
                              'm')
                   ELSE
                       CONCAT(epi.duration % 60, 'm')
                   END
                                                            AS duration_formatted,
               IF(
                       ide.id IS NOT NULL,
                       JSON_UNQUOTE(JSON_EXTRACT(ide.name, '$.fr')),
                       'Orateur'
               )                                            as type,
               JSON_UNQUOTE(JSON_EXTRACT(epi.name, '$.fr')) AS title,
               JSON_UNQUOTE(JSON_EXTRACT(eps.name, '$.fr')) AS session
        FROM event_program_interventions epi
                 JOIN event_program_sessions eps ON epi.event_program_session_id = eps.id
                 LEFT JOIN event_program_intervention_orators epio
                           ON epio.event_program_intervention_id = epi.id
                 LEFT JOIN events_contacts ec ON epio.events_contacts_id = ec.id
                 LEFT JOIN dictionnary_entries ide ON epi.specificity_id = ide.id
;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_dashboard_intervention_view");
    }
};
