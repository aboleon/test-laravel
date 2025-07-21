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
        Schema::table('order_cart_accommodation', function (Blueprint $table)   {
            $table->foreignId("room_group_id")->nullable()->after('event_hotel_id')->constrained('event_accommodation_room_groups');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_accommodation', function (Blueprint $table)   {
            $table->dropForeign('order_cart_accommodation_room_group_id_foreign');
            $table->dropColumn('room_group_id');
        });

    }
};
