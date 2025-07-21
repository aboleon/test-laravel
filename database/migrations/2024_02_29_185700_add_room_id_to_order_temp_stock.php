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
        Schema::table('order_temp_stock', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->constrained('event_accommodation_room')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_temp_stock', function (Blueprint $table) {
            $table->dropForeign('order_temp_stock_room_id_foreign');
            $table->dropColumn('room_id');
        });
    }
};
