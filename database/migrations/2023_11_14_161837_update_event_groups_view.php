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
        DB::statement("CREATE OR REPLACE VIEW event_groups_view AS
        SELECT
            eg.id,
            g.id AS group_id,
            g.name AS group_name,
            g.company AS group_company,
            DATE(eg.created_at) as event_group_created_at,
            eg.event_id,
            eg.comment,
            u.id AS user_id,
            CONCAT(u.first_name, ' ', u.last_name) AS main_contact_name,
            u.email AS main_contact_email,
            p.phone AS main_contact_phone,
            JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS main_contact_country,
            (
                SELECT COUNT(DISTINCT ec.id)
                FROM 
                    events_contacts ec
                LEFT JOIN group_contacts gc ON gc.user_id = ec.user_id
                WHERE gc.group_id = g.id
                AND ec.event_id = eg.event_id
            ) AS participants_count
            
        FROM
            `groups` g
            JOIN event_groups eg ON g.id = eg.group_id
            LEFT JOIN users u ON g.main_contact_id = u.id
            LEFT JOIN main_account_phone_view p ON u.id = p.user_id
            LEFT JOIN main_account_address_view a ON u.id = a.user_id
            LEFT JOIN countries c ON a.country_code = c.code
        WHERE
            g.deleted_at IS NULL
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS event_groups_view");
    }
};
