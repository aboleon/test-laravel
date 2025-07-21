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
        DB::statement(
            'ALTER TABLE `front_transactions`
	ADD COLUMN `order_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `payment_call_id`,
	ADD CONSTRAINT `FK_front_transactions_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE NO ACTION ON DELETE RESTRICT;
',
        );
        DB::statement(
            'ALTER TABLE `front_payment_calls`
	ADD COLUMN `order_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `cart_id`,
	ADD CONSTRAINT `FK_front_payment_calls_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE;
',
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_payment_calls', function (Blueprint $table) {
           $table->dropForeign('FK_front_payment_calls_orders');
           $table->dropColumn('order_id');
        });
        Schema::table('front_transactions', function (Blueprint $table) {
            $table->dropForeign('FK_front_transactions_orders');
            $table->dropColumn('order_id');
        });
    }
};
