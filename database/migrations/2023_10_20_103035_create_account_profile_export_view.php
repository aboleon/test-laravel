<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS account_profile_export_view");
        DB::statement("CREATE VIEW account_profile_export_view AS

SELECT 
    ap.id,
    ap.user_id,
    u.first_name,
    u.last_name,
    ap.account_type,
    JSON_UNQUOTE(JSON_EXTRACT(e1.name, '$.fr')) AS base,
    JSON_UNQUOTE(JSON_EXTRACT(e2.name, '$.fr')) AS domain,
    JSON_UNQUOTE(JSON_EXTRACT(e3.name, '$.fr')) AS title,
    JSON_UNQUOTE(JSON_EXTRACT(e4.name, '$.fr')) AS profession,
    JSON_UNQUOTE(JSON_EXTRACT(e5.name, '$.fr')) AS savant_society,
    ap.civ,
    ap.birth,
    ap.cotisation_year,
    ap.blacklisted,
    CONCAT(creator.first_name, ' ', creator.last_name) AS created_by,
    ap.blacklist_comment,
    ap.notes,
    ap.function,
    ap.passport_first_name,
    ap.passport_last_name,
    ap.rpps,
    es.name as establishment_name,
    es.country_code as establishment_country_code,
    es.type as establishment_type,
    es.street_number as establishment_street_number,
    es.postal_code as establishment_postal_code,
    es.locality as establishment_locality,
    es.administrative_area_level_1 as establishment_administrative_area_level_1,
    es.administrative_area_level_2 as establishment_administrative_area_level_2,
    es.text_address as establishment_text_address,
    CASE 
        WHEN addr.billing = 1 THEN 'Oui'
        ELSE 'Non'
    END AS main_address_billing,
    addr.street_number AS main_address_street_number,
    addr.route AS main_address_route,
    addr.locality AS main_address_locality,
    addr.postal_code AS main_address_postal_code,
    addr.country_code AS main_address_country_code,
    addr.administrative_area_level_1 AS main_address_administrative_area_level_1,
    addr.administrative_area_level_2 AS main_address_administrative_area_level_2,
    addr.text_address AS main_address_text_address,
    addr.company AS main_address_company,
    ph.phone AS main_phone
FROM account_profile ap
INNER JOIN users u ON ap.user_id = u.id
INNER JOIN users creator ON ap.created_by = creator.id
LEFT JOIN main_account_address_view addr ON ap.user_id = addr.user_id
LEFT JOIN main_account_phone_view ph ON ap.user_id = ph.user_id
LEFT JOIN dictionnary_entries e1 ON ap.base_id = e1.id
LEFT JOIN dictionnary_entries e2 ON ap.domain_id = e2.id
LEFT JOIN dictionnary_entries e3 ON ap.title_id = e3.id
LEFT JOIN dictionnary_entries e4 ON ap.profession_id = e4.id
LEFT JOIN dictionnary_entries e5 ON ap.savant_society_id = e5.id
LEFT JOIN establishments es ON ap.establishment_id = es.id;



");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS account_profile_export_view");
    }
};
