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
        DB::statement("CREATE VIEW sellable_view as
        select
            a.id,
            a.deleted_at,
            JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.fr')) as title_fr,
            JSON_UNQUOTE(JSON_EXTRACT(a.title, '$.en')) as title_en,
            a.price,
            a.sold_per,
            CASE
    WHEN a.published = 1 THEN 'Oui'
    WHEN a.published IS NULL THEN 'Non'
  END AS published,
            JSON_UNQUOTE(JSON_EXTRACT(b.name, '$.fr')) as category_fr,
            JSON_UNQUOTE(JSON_EXTRACT(b.name, '$.en')) as category_en
        FROM sellables a
        LEFT JOIN dictionnary_entries b ON b.id = a.category_id"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement("DROP VIEW IF EXISTS sellable_view");
    }
};
