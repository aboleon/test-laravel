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
        Schema::table('orders', function (Blueprint $table) {
           $table->foreignId('amended_accommodation_cart')->nullable()->constrained('order_cart_accommodation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `orders` DROP FOREIGN KEY `orders_amended_accommodation_cart_foreign`');
        DB::statement('ALTER TABLE `orders` DROP COLUMN `amended_accommodation_cart`');
    }
};
