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
        DB::statement("ALTER TABLE `event_grant_quota` CHANGE COLUMN `order_id` `order_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `id`");
        DB::statement("ALTER TABLE `event_grant_quota`
	ADD COLUMN `front_cart_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `grant_id`,
	ADD CONSTRAINT `FK_event_grant_quota_front_carts` FOREIGN KEY (`front_cart_id`) REFERENCES `front_carts` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE;
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `event_grant_quota` CHANGE COLUMN `order_id` `order_id` BIGINT(20) UNSIGNED NULL AFTER `id`");
        DB::statement("ALTER TABLE `event_grant_quota`
	DROP COLUMN `front_cart_id`,
	DROP FOREIGN KEY `FK_event_grant_quota_front_carts`;
");
    }
};
