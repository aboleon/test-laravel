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
        Schema::rename('order_cart_service_attributions', 'order_service_attributions');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('order_service_attributions', 'order_cart_service_attributions');
    }
};
