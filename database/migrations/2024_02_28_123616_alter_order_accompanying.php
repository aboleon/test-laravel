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
        Schema::table('order_accompanying', function (Blueprint $table) {
            $table->foreignId('room_id')->after('order_id')->constrained('event_accommodation_room')->cascadeOnDelete();
        });

        Schema::dropIfExists('accommodation_accompanying');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_accompanying', function (Blueprint $table) {
            $table->dropForeign('order_accompanying_room_id_foreign');
            $table->dropColumn('room_id');
        });

        Schema::create('accommodation_accompanying', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart_accommodation')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('event_accommodation_room')->cascadeOnDelete();
            $table->unsignedSmallInteger('total');
            $table->text('names');
        });
    }
};
