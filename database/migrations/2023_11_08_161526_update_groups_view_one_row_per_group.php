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
        DB::statement("CREATE OR REPLACE VIEW group_view AS
        SELECT
         g.id,
         g.name, 
         g.company, 
         g.deleted_at, 
        JSON_UNQUOTE(JSON_EXTRACT(c.name, '$.fr')) AS country
        
        FROM `groups` g
        LEFT JOIN main_group_address_view a on a.group_id = g.id
        LEFT JOIN countries c ON a.country_code = c.code
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS group_view");
    }
};
