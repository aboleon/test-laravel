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
        Schema::create('order_cart_service_attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_contact_id')->constrained('events_contacts')->cascadeOnDelete();
            $table->foreignId('cart_id')->constrained('order_cart_service')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_service_attributions');
    }
};
