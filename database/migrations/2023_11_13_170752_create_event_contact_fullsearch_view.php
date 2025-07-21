<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_full_search_view");
        DB::statement("CREATE VIEW event_contact_full_search_view AS

SELECT 
    ap.*,
    u.first_name,
    u.last_name,
    u.email,
    CASE WHEN u.deleted_at IS NULL THEN 0 ELSE 1 END as is_archived,
        
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(e1.name, '$.fr'))) AS base,
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(e2.name, '$.fr'))) AS domain,
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(e3.name, '$.fr'))) AS title,
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(e4.name, '$.fr'))) AS profession,
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(e5.name, '$.fr'))) AS savant_society,
    CONCAT(creator.first_name, ' ', creator.last_name) AS created_by_fullname,
    
    es.name as establishment_name,
    es.country_code as establishment_country_code,
    es.type as establishment_type,
    es.street_number as establishment_street_number,
    es.postal_code as establishment_postal_code,
    es.locality as establishment_locality,
    es.administrative_area_level_1 as establishment_administrative_area_level_1,
    es.administrative_area_level_2 as establishment_administrative_area_level_2,
    es.text_address as establishment_text_address,
    
    -- Address 1 fields
    addr1.street_number AS address_1_street_number,
    addr1.route AS address_1_route,
    addr1.locality AS address_1_locality,
    addr1.postal_code AS address_1_postal_code,
    addr1.country_code AS address_1_country_code,
    addr1.administrative_area_level_1 AS address_1_administrative_area_level_1,
    addr1.administrative_area_level_2 AS address_1_administrative_area_level_2,
    addr1.text_address AS address_1_text_address,
    addr1.company AS address_1_company,
    -- Address 2 fields
    addr2.street_number AS address_2_street_number,
    addr2.route AS address_2_route,
    addr2.locality AS address_2_locality,
    addr2.postal_code AS address_2_postal_code,
    addr2.country_code AS address_2_country_code,
    addr2.administrative_area_level_1 AS address_2_administrative_area_level_1,
    addr2.administrative_area_level_2 AS address_2_administrative_area_level_2,
    addr2.text_address AS address_2_text_address,
    addr2.company AS address_2_company,
    -- Address 3 fields
    addr3.street_number AS address_3_street_number,
    addr3.route AS address_3_route,
    addr3.locality AS address_3_locality,
    addr3.postal_code AS address_3_postal_code,
    addr3.country_code AS address_3_country_code,
    addr3.administrative_area_level_1 AS address_3_administrative_area_level_1,
    addr3.administrative_area_level_2 AS address_3_administrative_area_level_2,
    addr3.text_address AS address_3_text_address,
    addr3.company AS address_3_company,
    -- Phone numbers
    ph1.phone AS phone_1,
    ph2.phone AS phone_2,
    ph3.phone AS phone_3,
    -- event contact
    ec.participation_type_group,
    ec.participation_type_id,
    LOWER(JSON_UNQUOTE(JSON_EXTRACT(pt.name, '$.fr'))) AS participation_type,
    ec.is_attending,
    ec.comment,
    grp.group_names AS `group`,
    grp.group_ids
    
    
FROM account_profile ap
INNER JOIN users u ON ap.user_id = u.id
INNER JOIN users creator ON ap.created_by = creator.id
-- Addresses JOIN
LEFT JOIN (
    SELECT *, ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY id) as rn
    FROM account_address
) addr1 ON ap.user_id = addr1.user_id AND addr1.rn = 1

LEFT JOIN (
    SELECT *, ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY id) as rn
    FROM account_address
) addr2 ON ap.user_id = addr2.user_id AND addr2.rn = 2

LEFT JOIN (
    SELECT *, ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY id) as rn
    FROM account_address
) addr3 ON ap.user_id = addr3.user_id AND addr3.rn = 3

-- Phones JOIN
LEFT JOIN (
    SELECT *, ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY id) as rn
    FROM account_phones
) ph1 ON ap.user_id = ph1.user_id AND ph1.rn = 1

LEFT JOIN (
    SELECT *, ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY id) as rn
    FROM account_phones
) ph2 ON ap.user_id = ph2.user_id AND ph2.rn = 2

LEFT JOIN (
    SELECT *, ROW_NUMBER() OVER(PARTITION BY user_id ORDER BY id) as rn
    FROM account_phones
) ph3 ON ap.user_id = ph3.user_id AND ph3.rn = 3
LEFT JOIN dictionnary_entries e1 ON ap.base_id = e1.id
LEFT JOIN dictionnary_entries e2 ON ap.domain_id = e2.id
LEFT JOIN dictionnary_entries e3 ON ap.title_id = e3.id
LEFT JOIN dictionnary_entries e4 ON ap.profession_id = e4.id
LEFT JOIN dictionnary_entries e5 ON ap.savant_society_id = e5.id
LEFT JOIN establishments es ON ap.establishment_id = es.id
LEFT JOIN events_contacts ec ON ec.user_id = ap.user_id
LEFT JOIN participation_types pt ON pt.id = ec.participation_type_id
LEFT JOIN (
    SELECT 
        gc.user_id,
        GROUP_CONCAT(g.name SEPARATOR ', ') AS group_names,
        CONCAT(',', GROUP_CONCAT(g.id SEPARATOR ','), ',') AS group_ids
    FROM group_contacts gc
    INNER JOIN `groups` g ON gc.group_id = g.id
    GROUP BY gc.user_id
) grp ON u.id = grp.user_id


;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_contact_full_search_view");
    }
};
