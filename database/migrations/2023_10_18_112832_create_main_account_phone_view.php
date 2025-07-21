<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE VIEW main_account_phone_view AS
WITH PrioritizedPhones AS (
    SELECT *,
           CASE
               WHEN `default` IS NOT NULL THEN 1
               ELSE 2
               END AS priority
    FROM account_phones
)

SELECT *
FROM PrioritizedPhones
WHERE (user_id, priority, id) IN (
    SELECT user_id,
           MIN(priority) AS min_priority,
           MIN(id) as min_id
    FROM PrioritizedPhones
    GROUP BY user_id
)

");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS main_account_phone_view");
    }
};
