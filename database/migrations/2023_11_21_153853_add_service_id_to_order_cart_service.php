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
        DB::statement("DELETE FROM order_cart_service");
        Schema::table('order_cart_service', function (Blueprint $table) {
            $table->foreignId('service_id')->after('cart_id')->constrained('event_sellable_service')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_service', function (Blueprint $table) {
            $table->dropForeign('order_cart_service_service_id_foreign');
            $table->dropColumn('service_id');
        });
    }
};
