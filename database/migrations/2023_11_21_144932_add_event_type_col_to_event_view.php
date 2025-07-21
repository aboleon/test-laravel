<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("CREATE OR REPLACE VIEW event_view AS  
        SELECT 
            e.id, 
            e.bank_card_code as codecb,
            DATE_FORMAT(e.starts, '%d/%m/%Y') as starts,
            DATE_FORMAT(e.ends, '%d/%m/%Y') as ends,
            JSON_UNQUOTE(JSON_EXTRACT(et.name, '$.fr')) AS name,
            JSON_UNQUOTE(JSON_EXTRACT(et.subname, '$.fr')) AS subname,
            JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) AS parent,
            JSON_UNQUOTE(JSON_EXTRACT(d2.name, '$.fr')) AS type,
            CONCAT_WS(' ', u.first_name, u.last_name) as admin,
            CASE
                WHEN e.published = 1 THEN 'Oui'
                WHEN e.published IS NULL THEN 'Non'
                ELSE 'other'
            END AS published
        FROM events e
        LEFT JOIN events_texts et ON e.id = et.event_id
        LEFT JOIN users u ON e.admin_id = u.id
        LEFT JOIN dictionnary_entries d ON e.event_main_id = d.id
        LEFT JOIN dictionnary_entries d2 ON e.event_type_id = d2.id
");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_view");
    }
};
