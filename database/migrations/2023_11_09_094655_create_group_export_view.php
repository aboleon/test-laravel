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
        a.administrative_area_level_1 as main_address_administrative_area_level_1,
        a.administrative_area_level_1_short as main_address_administrative_area_level_1_short,
        a.administrative_area_level_2 as main_address_administrative_area_level_2,
        a.text_address as main_address_text_address,
        JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS main_address_country_name,
        
        u.id as contact_user_id,
        u.first_name as contact_first_name,
        u.last_name as contact_last_name,
        ap.account_type as contact_account_type,
        JSON_UNQUOTE(JSON_EXTRACT(e1.name, '$.fr')) AS contact_base,
        JSON_UNQUOTE(JSON_EXTRACT(e2.name, '$.fr')) AS contact_domain,
        JSON_UNQUOTE(JSON_EXTRACT(e3.name, '$.fr')) AS contact_title,
        JSON_UNQUOTE(JSON_EXTRACT(e4.name, '$.fr')) AS contact_profession,
        JSON_UNQUOTE(JSON_EXTRACT(e5.name, '$.fr')) AS contact_savant_society,
        ap.civ as contact_civ,
        ap.birth as contact_birth,
        ap.cotisation_year as contact_cotisation_year,
        ap.blacklisted as contact_blacklisted,
        ap.blacklist_comment as contact_blacklist_comment,
        ap.notes as contact_notes,
        ap.function as contact_function,
        ap.rpps as contact_rpps
        
        FROM `groups` g
        LEFT JOIN main_group_address_view a on a.group_id = g.id
        LEFT JOIN countries c ON a.country_code = c.code
        LEFT JOIN account_profile ap ON ap.user_id = g.main_contact_id
        LEFT JOIN users u ON ap.user_id = u.id
        LEFT JOIN dictionnary_entries e1 ON ap.base_id = e1.id
        LEFT JOIN dictionnary_entries e2 ON ap.domain_id = e2.id
        LEFT JOIN dictionnary_entries e3 ON ap.title_id = e3.id
        LEFT JOIN dictionnary_entries e4 ON ap.profession_id = e4.id
        LEFT JOIN dictionnary_entries e5 ON ap.savant_society_id = e5.id
        
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
