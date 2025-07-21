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
        Schema::rename('accommodation_cart', 'order_cart_accommodation');
        Schema::rename('service_cart', 'order_cart_service');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('order_cart_accommodation', 'accommodation_cart');
        Schema::rename('order_cart_service', 'service_cart');
    }
};
