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
        Schema::create('accommodation_accompanying', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart_accommodation')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('event_accommodation_room')->cascadeOnDelete();
            $table->unsignedSmallInteger('total');
            $table->text('names');
        });

        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->dropColumn('quantity_accompanying');
            $table->dropColumn('accompanying');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_accompanying');
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->unsignedInteger('quantity_accompanying')->default(0);
            $table->text('accompanying')->nullable();
        });
    }
};
