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
        DB::statement("CREATE OR REPLACE VIEW group_export_view AS
        SELECT
            g.id,
            g.name, 
            g.company, 
            g.billing_comment,
            g.siret,
            
        CASE 
            WHEN a.billing = 1 
                THEN 'Oui'
            ELSE 'Non'
        END AS main_address_billing,
        a.name as main_address_name,
        a.street_number as main_address_street_number,
        a.route as main_address_route,
        a.locality as main_address_locality,
        a.postal_code as main_address_postal_code,
        a.country_code as main_address_country_code,
        a.text_address as main_address_text_address,
        JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS main_address_country_name
        
        
        FROM `groups` g
        LEFT JOIN main_group_address_view a on a.group_id = g.id
        LEFT JOIN countries c ON a.country_code = c.code
        
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS group_export_view");
    }
};
