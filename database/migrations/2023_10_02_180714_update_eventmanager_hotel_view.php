<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW eventmanager_hotel_view AS
        select a.id, a.event_id, JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.fr')) AS title,
       b.locality, d.name, d.email, d.phone,
       CASE
    WHEN a.pec = 1 THEN 'Oui'
    WHEN a.pec IS NULL THEN 'Non'
  END AS pec,
       CASE
    WHEN a.published IS NULL THEN 'Hors ligne'
    ELSE 'En ligne'
  END AS published
    FROM event_accommodation a
    LEFT JOIN hotels d ON a.hotel_id = d.id
    LEFT JOIN hotel_address b ON a.hotel_id = b.hotel_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS eventmanager_hotel_view");
    }
};
