<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE `orders`
	CHANGE COLUMN `client_type` `client_type` ENUM('contact','group','orator') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `client_id`",
        );
        DB::statement(
            "
ALTER TABLE `order_invoiceable`
	CHANGE COLUMN `account_type` `account_type` ENUM('contact','group','congress') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `id`",
        );
        DB::statement(
            "ALTER TABLE `order_invoiceable`
	CHANGE COLUMN `country_code` `country_code` VARCHAR(2) NOT NULL DEFAULT 'FR' COLLATE 'utf8mb4_unicode_ci' AFTER `cedex`,
    CHANGE COLUMN `last_name` `last_name` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `first_name`"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "ALTER TABLE `orders`
	CHANGE COLUMN `client_type` `client_type` ENUM('contact','group') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `client_id`",
        );
        DB::statement(
            "
ALTER TABLE `order_invoiceable`
	CHANGE COLUMN `account_type` `account_type` ENUM('contact','group') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `id`",
        );
        DB::statement(
            "ALTER TABLE `order_invoiceable`
	CHANGE COLUMN `country_code` `country_code` VARCHAR(2) NULL COLLATE 'utf8mb4_unicode_ci' AFTER `cedex`,
            CHANGE COLUMN `last_name` `last_name` TEXT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `first_name`"
        );
    }
};
