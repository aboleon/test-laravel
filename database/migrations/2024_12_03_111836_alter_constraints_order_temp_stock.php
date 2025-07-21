<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE `order_temp_stock`
	DROP FOREIGN KEY `order_temp_stock_frontcartline_id_foreign`,
	DROP FOREIGN KEY `order_temp_stock_room_id_foreign`',
        );

        DB::statement(
            'ALTER TABLE `order_temp_stock`
	ADD CONSTRAINT `order_temp_stock_frontcartline_id_foreign` FOREIGN KEY (`frontcartline_id`) REFERENCES `front_cart_lines` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
	ADD CONSTRAINT `order_temp_stock_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
