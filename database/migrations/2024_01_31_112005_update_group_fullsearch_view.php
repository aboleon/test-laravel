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
        DB::statement("CREATE OR REPLACE VIEW group_fullsearch_view AS
        SELECT
            g.id,
            g.name, 
            g.company, 
            g.billing_comment,
            g.siret,
            g.created_by,
            g.vat_id,
            v.rate as vat_rate,
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
        JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS main_address_country_name,
        
        u.id as contact_user_id,
        u.first_name as contact_first_name,
        u.last_name as contact_last_name,
        u.email as contact_email,
        ap.account_type as contact_account_type,
        ap.base_id as contact_base_id,
        ap.domain_id as contact_domain_id,
        ap.title_id as contact_title_id,
        ap.profession_id as contact_profession_id,
        ap.language_id as contact_language_id,
        ap.savant_society_id as contact_savant_society_id,
        JSON_UNQUOTE(JSON_EXTRACT(e1.name, '$.fr')) AS contact_base,
        JSON_UNQUOTE(JSON_EXTRACT(e2.name, '$.fr')) AS contact_domain,
        JSON_UNQUOTE(JSON_EXTRACT(e3.name, '$.fr')) AS contact_title,
        JSON_UNQUOTE(JSON_EXTRACT(e4.name, '$.fr')) AS contact_profession,
        JSON_UNQUOTE(JSON_EXTRACT(e5.name, '$.fr')) AS contact_savant_society,
        JSON_UNQUOTE(JSON_EXTRACT(e6.name, '$.fr')) AS contact_language,
        ap.civ as contact_civ,
        ap.birth as contact_birth,
        ap.cotisation_year as contact_cotisation_year,
        ap.blacklisted as contact_blacklisted,
        ap.blacklist_comment as contact_blacklist_comment,
        ap.notes as contact_notes,
        ap.function as contact_function,
        ap.rpps as contact_rpps,
        
        uc.id as creator_user_id,
        uc.first_name as creator_first_name,
        uc.last_name as creator_last_name,
        uc.email as creator_email,
        apc.account_type as creator_account_type,
        apc.base_id as creator_base_id,
        apc.domain_id as creator_domain_id,
        apc.title_id as creator_title_id,
        apc.profession_id as creator_profession_id,
        apc.language_id as creator_language_id,
        apc.savant_society_id as creator_savant_society_id,
        JSON_UNQUOTE(JSON_EXTRACT(ec1.name, '$.fr')) AS creator_base,
        JSON_UNQUOTE(JSON_EXTRACT(ec2.name, '$.fr')) AS creator_domain,
        JSON_UNQUOTE(JSON_EXTRACT(ec3.name, '$.fr')) AS creator_title,
        JSON_UNQUOTE(JSON_EXTRACT(ec4.name, '$.fr')) AS creator_profession,
        JSON_UNQUOTE(JSON_EXTRACT(ec5.name, '$.fr')) AS creator_savant_society,
        JSON_UNQUOTE(JSON_EXTRACT(ec6.name, '$.fr')) AS creator_language,
        apc.civ as creator_civ,
        apc.birth as creator_birth,
        apc.cotisation_year as creator_cotisation_year,
        apc.blacklisted as creator_blacklisted,
        apc.blacklist_comment as creator_blacklist_comment,
        apc.notes as creator_notes,
        apc.function as creator_function,
        apc.rpps as creator_rpps
        
        
        
        
        FROM `groups` g
        LEFT JOIN main_group_address_view a on a.group_id = g.id
        LEFT JOIN countries c ON a.country_code = c.code
        LEFT JOIN vat v ON v.id = g.vat_id
        
        LEFT JOIN account_profile ap ON ap.user_id = g.main_contact_id
        LEFT JOIN users u ON ap.user_id = u.id
        LEFT JOIN dictionnary_entries e1 ON ap.base_id = e1.id
        LEFT JOIN dictionnary_entries e2 ON ap.domain_id = e2.id
        LEFT JOIN dictionnary_entries e3 ON ap.title_id = e3.id
        LEFT JOIN dictionnary_entries e4 ON ap.profession_id = e4.id
        LEFT JOIN dictionnary_entries e5 ON ap.savant_society_id = e5.id
        LEFT JOIN dictionnary_entries e6 ON ap.language_id = e6.id
        
        LEFT JOIN account_profile apc ON apc.user_id = g.created_by
        LEFT JOIN users uc ON apc.user_id = uc.id
        LEFT JOIN dictionnary_entries ec1 ON apc.base_id = ec1.id
        LEFT JOIN dictionnary_entries ec2 ON apc.domain_id = ec2.id
        LEFT JOIN dictionnary_entries ec3 ON apc.title_id = ec3.id
        LEFT JOIN dictionnary_entries ec4 ON apc.profession_id = ec4.id
        LEFT JOIN dictionnary_entries ec5 ON apc.savant_society_id = ec5.id
        LEFT JOIN dictionnary_entries ec6 ON apc.language_id = ec6.id        
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS group_fullsearch_view");
    }
};
