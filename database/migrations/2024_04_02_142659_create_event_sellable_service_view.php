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
        DB::statement("CREATE OR REPLACE VIEW event_sellable_service_view as
        SELECT
            s.id,
            s.event_id,
            s.title,
            s.is_invitation,
            CASE 
                WHEN s.is_invitation = 1 
                THEN 'Oui'
                ELSE 'Non'
            END as is_invitation_display,
            JSON_UNQUOTE(JSON_EXTRACT(sg.name, '$.fr')) as group_fr,
            DATE_FORMAT(s.service_date, '%d/%m/%Y') as service_date_fr,
            s.stock_initial,
            (CAST(s.stock_initial AS SIGNED) - CAST(s.stock AS SIGNED)) AS reserved,
            s.stock,
            s.published,
            s.pec_eligible,
            GROUP_CONCAT(DISTINCT CAST(esp.price / 100 AS UNSIGNED) ORDER BY esp.price ASC SEPARATOR ',<br>') AS prices
        
        FROM event_sellable_service s
        LEFT JOIN dictionnary_entries sg ON sg.id = s.service_group
        LEFT JOIN event_sellable_service_prices esp ON esp.event_sellable_service_id = s.id
        GROUP BY s.id
        "
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW event_sellable_service_view');
    }
};
