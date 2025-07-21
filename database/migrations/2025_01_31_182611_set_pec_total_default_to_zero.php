<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE `front_cart_lines`
	CHANGE COLUMN `total_pec` `total_pec` INT(10) UNSIGNED NULL DEFAULT '0' AFTER `total_ttc`,
	CHANGE COLUMN `quantity` `quantity` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `unit_ttc`
",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            "ALTER TABLE `front_cart_lines`
	CHANGE COLUMN `quantity` `quantity` MEDIUMINT UNSIGNED NOT NULL AFTER `unit_ttc`,
	CHANGE COLUMN `total_pec` `total_pec` INT(10) UNSIGNED NULL AFTER `total_ttc`
",
        );
    }
};
