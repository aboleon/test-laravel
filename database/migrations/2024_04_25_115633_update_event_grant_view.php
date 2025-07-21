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
      CREATE OR REPLACE VIEW event_grant_view AS
SELECT
    eg.id,
    eg.deleted_at,
    eg.event_id,
    JSON_UNQUOTE(JSON_EXTRACT(eg.title, '$.fr')) as title,
    COALESCE(CONCAT(egc.first_name, ' ', egc.last_name), 'N/A') as contact, 
    JSON_UNQUOTE(JSON_EXTRACT(eg.comment, '$.fr')) as comment,
    eg.amount_ht,
    eg.amount_ht_used,
    eg.amount_ht - eg.amount_ht_used AS amount_ht_remaining,
    eg.amount_ttc,
    eg.amount_ttc_used,
    eg.amount_ttc - eg.amount_ttc_used AS amount_ttc_remaining,
    FORMAT(eg.amount_ht / 100.0, 2) AS amount_ht_display,
    FORMAT(eg.amount_ht_used / 100.0, 2) AS amount_ht_used_display,
    FORMAT((eg.amount_ht - eg.amount_ht_used) / 100.0, 2) AS amount_ht_remaining_display,
    FORMAT(eg.amount_ttc / 100.0, 2) AS amount_ttc_display,
    FORMAT(eg.amount_ttc_used / 100.0, 2) AS amount_ttc_used_display,
    FORMAT((eg.amount_ttc - eg.amount_ttc_used) / 100.0, 2) AS amount_ttc_remaining_display,
    
    eg.pec_fee,
    FORMAT(eg.pec_fee / 100.0, 2) AS pec_fee_display,
    eg.pax_avg,
    eg.pax_max,
    eg.active
FROM event_grant eg 
LEFT JOIN event_grant_contact egc ON eg.id = egc.grant_id


        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_grant_view");
    }
};
