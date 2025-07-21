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
        Schema::create('accommodation_cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart')->cascadeOnDelete();
            $table->date('date')->index();
            $table->foreignId('event_hotel_id')->constrained('event_accommodation')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('event_accommodation_room')->restrictOnDelete();
            $table->foreignId('vat_id')->constrained('vat')->restrictOnDelete();
            $table->unsignedInteger('unit_price')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('total_net')->default(0);
            $table->unsignedInteger('total_vat')->default(0);
            $table->unsignedInteger('total_pec')->default(0);
            $table->unsignedInteger('quantity_accompagnying')->default(0);
            $table->text('accompagnying')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_cart');
    }
};
