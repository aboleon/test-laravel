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
        Schema::table('service_cart', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained('event_sellable_service')->restrictOnDelete();
            $table->renameColumn('price_unit','unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_cart', function (Blueprint $table) {
            $table->dropForeign('service_cart_service_id_foreign');
            $table->dropColumn('service_id');
            $table->renameColumn('unit_price','price_unit');
        });
    }
};
