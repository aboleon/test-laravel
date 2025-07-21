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
        Schema::create('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_contact_id')->constrained('events_contacts')->cascadeOnDelete();
            $table->foreignId('cart_id')->constrained('order_cart')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('event_accommodation_room')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('order_cart_service_attributions', function (Blueprint $table) {
            $table->dropForeign('order_cart_service_attributions_cart_id_foreign');
            $table->foreign('cart_id')->references('id')->on('order_cart')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_accommodation_attributions');
    }
};
