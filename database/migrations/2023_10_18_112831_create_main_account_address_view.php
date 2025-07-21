<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW main_account_address_view AS
WITH PrioritizedAddresses AS (
    SELECT *,
           CASE
               WHEN company IS NOT NULL THEN 1
               WHEN billing = 1 THEN 2
               ELSE 3
               END AS priority
    FROM account_address
)

SELECT *
FROM PrioritizedAddresses
WHERE (user_id, priority, id) IN (
    SELECT user_id,
           MIN(priority) AS min_priority,
           MIN(id) as min_id
    FROM PrioritizedAddresses
    GROUP BY user_id
)
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS main_account_address_view");
    }
};
