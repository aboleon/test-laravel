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
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            DB::statement('ALTER TABLE `order_cart_accommodation` CHANGE `cancellation_request` `cancellation_request` TIMESTAMP NULL DEFAULT NULL');
            $table->timestamp('cancelled_at')->nullable();
        });

        Schema::table('order_cart_service', function (Blueprint $table) {
            DB::statement('ALTER TABLE `order_cart_service` CHANGE `cancellation_request` `cancellation_request` TIMESTAMP NULL DEFAULT NULL');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('cancellation_request')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cancellation_request');
            $table->dropColumn('cancelled_at');
        });
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
        });
    }
};
