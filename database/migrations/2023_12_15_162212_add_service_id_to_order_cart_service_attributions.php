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
        Schema::table('order_cart_service_attributions', function (Blueprint $table) {
            $table->foreignId('service_id')->after('cart_id')->constrained('event_sellable_service')->restrictOnDelete();
            $table->dropForeign('order_cart_service_attributions_cart_id_foreign');
            $table->foreign('cart_id')->references('cart_id')->on('order_cart_service')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_service_attributions', function (Blueprint $table) {
            $table->dropForeign('order_cart_service_attributions_service_id_foreign');
            $table->dropColumn('service_id');
        });
    }
};
