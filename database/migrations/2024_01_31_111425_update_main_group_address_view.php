<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS main_group_address_view");
        DB::statement("CREATE VIEW main_group_address_view AS
WITH PrioritizedAddresses AS (
    SELECT *,
           CASE
               WHEN billing = 1 THEN 1
               ELSE 2
               END AS priority
    FROM group_address
)

SELECT *
FROM PrioritizedAddresses
WHERE (group_id, priority, id) IN (
    SELECT group_id,
           MIN(priority) AS min_priority,
           MIN(id) as min_id
    FROM PrioritizedAddresses
    GROUP BY group_id
)
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS main_group_address_view");
    }
};
