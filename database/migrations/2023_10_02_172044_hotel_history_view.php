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
        DB::statement("CREATE VIEW hotel_history_view as
        select b.name as hotel,
       DATE_FORMAT(c.starts, '%d/%m/%Y') as event_starts, DATE_FORMAT(c.ends, '%d/%m/%Y') as event_ends,
       JSON_UNQUOTE(JSON_EXTRACT(d.name, '$.fr')) AS event,
       e.locality,
       JSON_UNQUOTE(JSON_EXTRACT(f.name, '$.fr')) AS country

    FROM event_accommodation a
        JOIN hotels b ON a.hotel_id = b.id
        JOIN events c on a.event_id = c.id
        JOIN events_texts d on a.event_id = d.event_id
        LEFT JOIN hotel_address e ON a.hotel_id = e.hotel_id
        LEFT JOIN countries f ON e.country_code = f.code
        ORDER BY c.starts
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS hotel_history");
    }
};
