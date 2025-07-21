<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE `front_payment_calls`
	CHANGE COLUMN `cart_id` `cart_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `provider`',
        );
        DB::statement(
            'ALTER TABLE `front_payment_calls`
	ADD COLUMN `group_manager_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `cart_id`
',
        );
        DB::statement(
            'ALTER TABLE `front_payment_calls`
	ADD CONSTRAINT `FK_front_payment_calls_events_contacts` FOREIGN KEY (`group_manager_id`) REFERENCES `events_contacts` (`id`) ON UPDATE NO ACTION ON DELETE RESTRICT

',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'ALTER TABLE `front_payment_calls`
	DROP FOREIGN KEY `FK_front_payment_calls_events_contacts`',
        );

        DB::statement(
            'ALTER TABLE `front_payment_calls`
	DROP COLUMN `group_manager_id`',
        );
    }
};
