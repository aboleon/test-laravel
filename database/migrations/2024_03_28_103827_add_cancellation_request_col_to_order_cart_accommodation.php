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
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            if (false === Schema::hasColumn('order_cart_accommodation', 'cancellation_request')) {
                $table->boolean('cancellation_request')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            if (true === Schema::hasColumn('order_cart_accommodation', 'cancellation_request')) {
                $table->dropColumn('cancellation_request');
            }
        });
    }
};
