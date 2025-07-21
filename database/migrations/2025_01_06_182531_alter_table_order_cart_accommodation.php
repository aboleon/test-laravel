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
        DB::statement("ALTER TABLE `order_cart_accommodation` ADD COLUMN `cancelled_qty` INT UNSIGNED NULL DEFAULT '0' AFTER `cancelled_at`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `order_cart_accommodation` DROP COLUMN `cancelled_qty`");
    }
};
