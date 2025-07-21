<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("DROP VIEW IF EXISTS event_contact_dashboard_session_view");
        DB::statement("CREATE VIEW event_contact_dashboard_session_view AS


SELECT ec2.id                                       AS event_contact_id,
       eps2.id                                      AS session_id,
       IF(
               mde.id IS NOT NULL,
               JSON_UNQUOTE(JSON_EXTRACT(mde.name, '$.fr')),
               'Modérateur'
       )                                            as type,
       JSON_UNQUOTE(JSON_EXTRACT(eps2.name, '$.fr')) AS session
FROM event_program_sessions eps2
         LEFT JOIN event_program_session_moderators epsm
                   ON epsm.event_program_session_id = eps2.id
         LEFT JOIN events_contacts ec2 ON epsm.events_contacts_id = ec2.id
         LEFT JOIN dictionnary_entries mde ON epsm.moderator_type_id = mde.id
;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_dashboard_session_view");
    }
};
