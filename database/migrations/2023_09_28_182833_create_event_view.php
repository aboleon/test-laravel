<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement("CREATE VIEW event_view AS select a.id, a.bank_card_code as codecb,
       DATE_FORMAT(a.starts, '%d/%m/%Y') as starts,
       DATE_FORMAT(a.ends, '%d/%m/%Y') as ends,
       JSON_UNQUOTE(JSON_EXTRACT(b.name, '$.fr')) AS name,
       JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) AS parent,
       CONCAT_WS(' ', c.first_name, c.last_name) as admin,
  CASE
    WHEN a.published = 1 THEN 'Oui'
    WHEN a.published IS NULL THEN 'Non'
    ELSE 'other'
  END AS published
FROM events a
LEFT JOIN events_texts b ON a.id = b.event_id
LEFT JOIN users c ON a.admin_id = c.id
LEFT JOIN dictionnary_entries d ON a.event_main_id = d.id");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_view");
    }
};
