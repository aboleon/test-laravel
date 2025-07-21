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
        Schema::create('order_cart_taxroom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart')->cascadeOnDelete();
            $table->foreignId('event_hotel_id')->constrained('event_accommodation')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('event_accommodation_room')->restrictOnDelete();
            $table->unsignedInteger('amount');
            $table->unsignedInteger('amount_net');
            $table->unsignedInteger('amount_vat');
            $table->foreignId('vat_id')->constrained('vat')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_taxroom');
    }
};
