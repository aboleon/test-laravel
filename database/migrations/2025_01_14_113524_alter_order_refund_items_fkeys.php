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
        DB::statement('ALTER TABLE `order_refunds_items`
	DROP FOREIGN KEY `order_refunds_items_refund_id_foreign`,
	DROP FOREIGN KEY `order_refunds_items_vat_id_foreign`');

        DB::statement('ALTER TABLE `order_refunds_items`
	ADD CONSTRAINT `order_refunds_items_refund_id_foreign` FOREIGN KEY (`refund_id`) REFERENCES `order_refunds` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
	ADD CONSTRAINT `order_refunds_items_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
