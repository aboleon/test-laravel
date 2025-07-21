<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE `event_deposits`
	CHANGE COLUMN `status` `status` ENUM('paid','refunded','billed','unpaid') NULL DEFAULT 'unpaid' COLLATE 'utf8mb4_unicode_ci' AFTER `total_vat`",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "ALTER TABLE `event_deposits`
	CHANGE COLUMN `status` `status` ENUM('paid','refunded','billed') NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `total_vat`",
        );
    }
};
